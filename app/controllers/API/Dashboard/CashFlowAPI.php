<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class CashFlowAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/ArusKasMdl');
    } /*}}}*/

    public function data_get () /*{{{*/
    {
        $data = array(
            'bid'   => get_var('bid'),
            'month' => intval(get_var('month', date('n'))),
            'year'  => intval(get_var('year', date('Y'))),
        );

        $mytipe = 'cf-direct';

        $rs_cabang = Modules::data_cabang_all('', $data['bid'], 'f');
        $data_cabang = $rs_cabang->fields;

        $rs = ArusKasMdl::list($mytipe, $data);

        $data_db = $data_pos = [];
        while (!$rs->EOF)
        {
            $bc = $rs->fields['branch_code'];
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

            $tmpdata = array();

            foreach ($data_cabang as $bc => $cabang)
                $tmpdata['amounts']['branches'][$bc] = $row['branches'][$bc]['amount'] ?? 0;

            $tmpdata['amounts']['total'] = $row['total']['amount'] ?? 0;

            $data_pos[$pcfid] = $tmpdata;
 
            $rs_pos->MoveNext();
        }

        $arus_kas_operasional = $data_pos[1]['amounts']['total'];
        $arus_kas_investasi = $data_pos[2]['amounts']['total'];
        $arus_kas_pendanaan = $data_pos[3]['amounts']['total'];

        $saldo_diff = $arus_kas_operasional + $arus_kas_investasi + $arus_kas_pendanaan;

        $data_cashflow = [
            'arus_kas_operasional'  => $arus_kas_operasional,
            'inflow_operasional'    => $data_pos[4]['amounts']['total'],
            'outflow_operasional'   => $data_pos[5]['amounts']['total'],
            'arus_kas_investasi'    => $arus_kas_investasi,
            'arus_kas_pendanaan'    => $arus_kas_pendanaan
        ];

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

        $data_saldo = [
            'awal'  => $saldo['total']['cf_speriod'],
            'akhir' => $saldo['total']['cf_eperiod'],
            'diff'  => $saldo_diff
        ];

        $dataJSON = array(
            'bulan'         => monthnamelong($data['month']),
            'year'          => $data['year'],
            'branch'        => $data_cabang,
            'data_cashflow' => $data_cashflow,
            'data_saldo'    => $data_saldo
        );

        $this->response($dataJSON, REST::HTTP_OK);
    } /*}}}*/
}
?>