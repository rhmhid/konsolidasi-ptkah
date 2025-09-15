<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class ArusKas extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/ArusKasMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        return view('akuntansi_report.arus_kas.list', compact(
            'sPeriod',
            'ePeriod'
        ));
    } /*}}}*/

    public function cetak ($mytipe) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

        if ($mytipe == 'cf-indirect') return self::cetak_indirect($mytipe, $data);
        else return self::cetak_direct($mytipe, $data);
    } /*}}}*/

    public function cetak_direct ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $tgl_cetak = date('Y-m-d');
        $rs_pos = $data_db = $data_mapping = $data_pos = [];
        $empty_pos = $without_mapping = true;
        $amount_cf = 0;

        $rs = ArusKasMdl::list($mytipe, $data);

        while (!$rs->EOF)
        {
            $data_db[$rs->fields['pcfid']]['amount'] += $rs->fields['amount'];
            $data_db[$rs->fields['parent_pcfid']]['amount'] += $rs->fields['amount'];
            $data_db[$rs->fields['pcfid_parent']]['amount'] += $rs->fields['amount'];

            $rs->MoveNext();
        }

        $rs_pos = ArusKasMdl::list_pos(1);

        while (!$rs_pos->EOF)
        {
            $row = $data_db[$rs_pos->fields['pcfid']];

            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pcfid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            if (trim($nama) == '') $nama = '&nbsp;';

            if ($is_header == 't') $nama = '<b>'.$nama.'</b>';
            else $nama = "<a href=\"javascript:void(0)\" onclick=\"detail_coa(".$rs_pos->fields['pcfid'].");\">".$nama."</a>";

            $tmpdata = array();
            $tmpdata['nama_pos']    = $space.$nama;
            $tmpdata['amount']      = $row['amount'];
            $tmpdata['level']       = $rs_pos->fields['level'];

            $data_mapping[$rs_pos->fields['pcfid']] = $tmpdata;
            $empty_pos = false;

            $rs_pos->MoveNext();
        }

        if (!empty($data_mapping))
        {
            foreach ($data_mapping as $pcfid => $rec)
            {
                $show_data = true;
                $background = '';

                if ($rec['amount'] == 0) $show_data = false;

                if ($show_data)
                {
                    if ($rec['level'] == 0)
                    {
                        $background = '#D9D9D9';
                        $amount_detail = "";
                        $amount_subheader = "";
                        $amount_header = '<b>'.format_uang($rec['amount'], 2).'</b>';

                        $amount_cf += $rec['amount'];
                    }
                    elseif ($rec['level'] == 1)
                    {
                        $amount_detail = "";
                        $amount_subheader = '<b>'.format_uang($rec['amount'], 2).'</b>';
                        $amount_header = "";
                    }
                    else
                    {
                        $amount_detail = format_uang($rec['amount'], 2);
                        $amount_subheader = "";
                        $amount_header = "";
                    }

                    $data_pos[$pcfid] = array(
                        'background'        => $background,
                        'nama_pos'          => $rec['nama_pos'],
                        'amount_detail'     => $amount_detail,
                        'amount_subheader'  => $amount_subheader,
                        'amount_header'     => $amount_header,
                    );
                }
            }
        }

        $rs_awal = ArusKasMdl::direct_saldo($mytipe, $data);

        $cf_speriod = $cf_eperiod = 0;
        while (!$rs_awal->EOF)
        {
            $cf_speriod += $rs_awal->fields['bamount'];
            $cf_eperiod += $rs_awal->fields['eamount'];

            $rs_awal->MoveNext();
        }

        if ($mytipe == 'cf-direct-daily')
        {
            $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' sd '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));
            $subtitle = 'DAILY';
        }
        else
        {
            if ($data['month'] <= 12)
                $periode = monthnamelong($data['month']).' '.$data['year'];
            else
                $periode = $data['month'].'-'.$data['year'];
        }

        if ($data_db[0]['amount'] <> 0)
        {
            $without_mapping = false;

            $pos_amount = format_uang($data_db[0]['amount'], 2);
        }

        return view('akuntansi_report.arus_kas.cetak_direct', compact(
            'subtitle',
            'mytipe',
            'data',
            'tgl_cetak',
            'periode',
            'data_mapping',
            'data_pos',
            'amount_cf',
            'empty_pos',
            'cf_speriod',
            'cf_eperiod',
            'without_mapping',
            'pos_amount'
        ));
    } /*}}}*/

    public function direct_coa ($mytipe, $myid) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $tgl_cetak = date('Y-m-d');

        if ($mytipe == 'cf-direct-daily')
        {
            $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' sd '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));
            $subtitle = 'DAILY';
        }
        else
        {
            if ($data['month'] <= 12)
                $periode = monthnamelong($data['month']).' '.$data['year'];
            else
                $periode = $data['month'].'-'.$data['year'];
        }

        $rs = ArusKasMdl::direct_coa($mytipe, $data, $myid);

        $cf_name = $myid == 0 ? 'POS ARUS KAS LAINNYA' : ArusKasMdl::get_cf_name($myid);

        return view('akuntansi_report.arus_kas.direct_coa', compact(
            'mytipe',
            'myid',
            'data',
            'tgl_cetak',
            'periode',
            'subtitle',
            'rs',
            'cf_name'
        ));
    } /*}}}*/
}
?>