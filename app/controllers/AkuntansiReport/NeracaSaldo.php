<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class NeracaSaldo extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaSaldoMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        return view('akuntansi_report.neraca_saldo.list', compact(
            'cmb_cabang'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $data_cabang = Modules::data_cabang_all($data['status_cabang']);

        // $empty_tb = true;
        // $data_tb = [];

        // $rs_period = Modules::get_period_akunting($data);

        // if (!$rs_period->EOF)
        // {
        //     $data['paid']   = $rs_period->fields['paid'];
        //     $data['pbegin'] = $rs_period->fields['pbegin'];
        //     $data['pend']   = $rs_period->fields['pend'];
        //     $data['sdate']  = $data['year'].'-'.$data['month'].'-01';
        //     $data['edate']  = date('Y-m-t', strtotime($data['sdate']));

        //     $rs = NeracaSaldoMdl::list($data);

        //     $data_db = array();
        //     while (!$rs->EOF)
        //     {
        //         $data_db[$rs->fields['coacode']] = array(
        //             'coaid'         => $rs->fields['coaid'],
        //             'coaname'       => $rs->fields['coaname'],
        //             'default_debet' => $rs->fields['default_debet'],
        //             'openingbal'    => floatval($rs->fields['openingbal']),
        //             'debet'         => floatval($rs->fields['debet']),
        //             'credit'        => floatval($rs->fields['credit']),
        //             'closingbal'    => floatval($rs->fields['closingbal']),
        //         );

        //         $rs->MoveNext();
        //     }

        //     $rss = Modules::laba_rugi($data);
        //     $coaid_laba_periode_lalu = Modules::$laba_periode_lalu;

        //     while (!$rss->EOF)
        //     {
        //         if ($rss->fields['coaid'] == $coaid_laba_periode_lalu)
        //         {
        //             $data_db[$rss->fields['coacode']] = array(
        //                 'coaid'         => $rss->fields['coaid'],
        //                 'coaname'       => $rss->fields['coaname'],
        //                 'default_debet' => $rss->fields['default_debet'],
        //                 'openingbal'    => floatval($rss->fields['closingbal']),
        //                 'debet'         => 0,
        //                 'credit'        => 0,
        //                 'closingbal'    => 0,
        //             );
        //         }

        //         $rss->MoveNext();
        //     }

        //     // ORDER BY lagi
        //     ksort($data_db);

        //     if (!empty($data_db))
        //     {
        //         $no = 1;
        //         $empty_tb = false;
        //         foreach ($data_db as $coacode => $tmp)
        //         {
        //             $balance = $tmp['default_debet'] == 't' ? ($tmp['debet'] - $tmp['credit']) : ($tmp['credit'] - $tmp['debet']);
        //             $balance += $tmp['openingbal'];

        //             $data_tb[] = array(
        //                 'no'        => $no++,
        //                 'coaid'     => $tmp['coaid'],
        //                 'coacode'   => $coacode,
        //                 'coaname'   => $tmp['coaname'],
        //                 'posisi'    => $tmp['default_debet'] == 't' ? 'Dr' : 'Cr',
        //                 'opening'   => $tmp['openingbal'],
        //                 'debet'     => $tmp['debet'],
        //                 'credit'    => $tmp['credit'],
        //                 'balance'   => $balance,
        //             );

        //             $tot_deb += $tmp['debet'];
        //             $tot_cre += $tmp['credit'];
        //         }
        //     }
        // }

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        return view('akuntansi_report.neraca_saldo.cetak', compact(
            'data',
            'report_month',
            'data_cabang',
            // 'data_tb',
            // 'tot_deb',
            // 'tot_cre'
        ));
    } /*}}}*/
}
?>