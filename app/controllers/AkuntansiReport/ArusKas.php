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

        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        return view('akuntansi_report.arus_kas.list', compact(
            'cmb_cabang',
            'sPeriod',
            'ePeriod'
        ));
    } /*}}}*/

    public function cetak ($mytipe) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
        );

        if ($mytipe == 'cf-indirect') return self::cetak_indirect($mytipe, $data);
        else return self::cetak_direct($mytipe, $data);
    } /*}}}*/

    public function cetak_direct ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $tgl_cetak = date('Y-m-d');
        $rs_pos = $data_db = $data_mapping = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $rs = ArusKasMdl::list($mytipe, $data);

        while (!$rs->EOF)
        {
            $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
            $pcfid = $rs->fields['pcfid'];
            $parent_pcfid = $rs->fields['parent_pcfid'];
            $pcfid_parent = $rs->fields['pcfid_parent'];
            $amt = $rs->fields['amount'];

            $data_db[$pcfid]['branches'][$bc]['amount'] = ($data_db[$pcfid]['branches'][$bc]['amount'] ?? 0) + $amt;
            $data_db[$parent_pcfid]['branches'][$bc]['amount'] = ($data_db[$parent_pcfid]['branches'][$bc]['amount'] ?? 0) + $amt;
            $data_db[$pcfid_parent]['branches'][$bc]['amount'] = ($data_db[$pcfid_parent]['branches'][$bc]['amount'] ?? 0) + $amt;

            $data_db[$pcfid]['total']['amount'] = ($data_db[$pcfid]['total']['amount'] ?? 0) + $amt;
            $data_db[$parent_pcfid]['total']['amount'] = ($data_db[$parent_pcfid]['total']['amount'] ?? 0) + $amt;
            $data_db[$pcfid_parent]['total']['amount'] = ($data_db[$pcfid_parent]['total']['amount'] ?? 0) + $amt;

            $rs->MoveNext();
        }

        $rs_pos = ArusKasMdl::list_pos(1);

        while (!$rs_pos->EOF)
        {
            $pcfid = $rs_pos->fields['pcfid'];
            $row = $data_db[$pcfid] ?? [];

            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pcfid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];
            if (trim($nama) == '') $nama = '&nbsp;';

            if ($is_header == 't') $nama = '<b>'.$nama.'</b>';
            else $nama = "<a href=\"javascript:void(0)\" onclick=\"detail_coa(".$rs_pos->fields['pcfid'].");\">".$nama."</a>";

            $tmpdata = array();
            $tmpdata['nama_pos']    = $space.$nama;
            $tmpdata['level']       = $rs_pos->fields['level'];

            foreach ($data_cabang as $bc => $cabang)
                $tmpdata['amounts']['branches'][$bc] = $row['branches'][$bc]['amount'] ?? 0;

            $tmpdata['amounts']['total'] = $row['total']['amount'] ?? 0;

            $data_mapping[$pcfid] = $tmpdata;
            $empty_pos = false;

            $rs_pos->MoveNext();
        }

        if (!empty($data_mapping))
        {
            foreach ($data_mapping as $pcfid => $rec)
            {
                $show_data = false;

                foreach ($data_cabang as $bc => $cabang)
                    if (($rec['amounts']['branches'][$bc] ?? 0) != 0) $show_data = true;

                if (($rec['amounts']['total'] ?? 0) != 0) $show_data = true;

                if ($show_data)
                {
                    $background = '';
                    $formatted_amounts = [];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $amt = $rec['amounts']['branches'][$bc] ?? 0;

                        $amount_detail = "";
                        $amount_subheader = "";
                        $amount_header = "";

                        if ($rec['level'] == 0)
                        {
                            $background = '#D9D9D9';
                            $amount_header = '<b>'.format_uang($amt, 2).'</b>';
                        }
                        elseif ($rec['level'] == 1)
                            $amount_subheader = '<b>'.format_uang($amt, 2).'</b>';
                        else
                            $amount_detail = format_uang($amt, 2);

                        $formatted_amounts['branches'][$bc] = [
                            'amount_detail'    => $amount_detail,
                            'amount_subheader' => $amount_subheader,
                            'amount_header'    => $amount_header,
                            'raw_amount'       => $amt
                        ];
                    }

                    $tot_amt = $rec['amounts']['total'] ?? 0;
                    $amount_detail = ""; $amount_subheader = ""; $amount_header = "";

                    if ($rec['level'] == 0)
                    {
                        $background = '#D9D9D9';
                        $amount_header = '<b>'.format_uang($tot_amt, 2).'</b>';
                    }
                    elseif ($rec['level'] == 1)
                        $amount_subheader = '<b>'.format_uang($tot_amt, 2).'</b>';
                    else
                        $amount_detail = format_uang($tot_amt, 2);

                    $formatted_amounts['total'] = [
                        'amount_detail'    => $amount_detail,
                        'amount_subheader' => $amount_subheader,
                        'amount_header'    => $amount_header,
                        'raw_amount'       => $tot_amt
                    ];

                    $data_pos[$pcfid] = array(
                        'background' => $background,
                        'nama_pos'   => $rec['nama_pos'],
                        'level'      => $rec['level'],
                        'amounts'    => $formatted_amounts
                    );
                }
            }
        }

        $rs_awal = ArusKasMdl::direct_saldo($mytipe, $data);
        $saldo = [];

        while (!$rs_awal->EOF)
        {
            $bc = $rs_awal->fields['branch_code'] ?? '';
            $bamount = $rs_awal->fields['bamount'];
            $eamount = $rs_awal->fields['eamount'];

            $saldo['branches'][$bc]['cf_speriod'] = ($saldo['branches'][$bc]['cf_speriod'] ?? 0) + $bamount;
            $saldo['branches'][$bc]['cf_eperiod'] = ($saldo['branches'][$bc]['cf_eperiod'] ?? 0) + $eamount;

            $saldo['total']['cf_speriod'] = ($saldo['total']['cf_speriod'] ?? 0) + $bamount;
            $saldo['total']['cf_eperiod'] = ($saldo['total']['cf_eperiod'] ?? 0) + $eamount;

            $rs_awal->MoveNext();
        }

        $subtitle = '';
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

        $pos_lainnya = [];
        if (isset($data_db[0]['total']['amount']) && $data_db[0]['total']['amount'] <> 0)
        {
            $without_mapping = false;
            foreach ($data_cabang as $bc => $cabang)
                $pos_lainnya['branches'][$bc] = format_uang($data_db[0]['branches'][$bc]['amount'] ?? 0, 2);

            $pos_lainnya['total'] = format_uang($data_db[0]['total']['amount'] ?? 0, 2);
        }

        return view('akuntansi_report.arus_kas.cetak_direct', compact(
            'subtitle',
            'mytipe',
            'data',
            'tgl_cetak',
            'periode',
            'data_mapping',
            'data_pos',
            'empty_pos',
            'saldo',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
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