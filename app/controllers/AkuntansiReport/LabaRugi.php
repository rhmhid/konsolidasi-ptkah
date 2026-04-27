<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class LabaRugi extends BaseController
{
    static $ho_jkk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/LabaRugiMdl');

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        return view('akuntansi_report.laba_rugi.list', compact(
            'cmb_cabang',
            'sPeriod',
            'ePeriod'
        ));
    } /*}}}*/

    public function cetak ($mytipe) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        if ($mytipe == 'pl-new') return self::cetak_baru($mytipe, $data);
        elseif ($mytipe == 'pl-new-daily') return self::cetak_baru_daily($mytipe, $data);
        elseif ($mytipe == 'pl-new-detail') return self::cetak_baru_detail($mytipe, $data);
        elseif ($mytipe == 'pl-new-detail-daily') return self::cetak_baru_detail_daily($mytipe, $data);

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $data_pl = $subtotals = [];
        $grand_totals = [
            'income' => ['branches' => [], 'total' => ['amount_bln_prev' => 0, 'amount_bln' => 0, 'closingbal' => 0]],
            'cost'   => ['branches' => [], 'total' => ['amount_bln_prev' => 0, 'amount_bln' => 0, 'closingbal' => 0]]
        ];

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $coatid = $rs->fields['coatid'];
                $coaid = $rs->fields['coaid'];

                if (!isset($data_pl[$coatid]))
                {
                    $data_pl[$coatid] = [
                        'headname'  => $coatid == 4 ? 'INCOME' : 'COST',
                        'data'      => []
                    ];
                }

                if (!isset($data_pl[$coatid]['data'][$coaid]))
                {
                    $data_pl[$coatid]['data'][$coaid] = [
                        'coacode' => $rs->fields['coacode'],
                        'coaname' => $rs->fields['coaname'],
                        'posisi'  => $rs->fields['default_debet'] == 't' ? 'Dr' : 'Cr',
                        'branches'=> [],
                        'total'   => [
                            'amount_bln_prev'   => 0,
                            'amount_bln'        => 0,
                            'closingbal'        => 0
                        ]
                    ];
                }

                $amt_prev = floatval($rs->fields['amount_bln_prev']);
                $amt_bln  = floatval($rs->fields['amount_bln']);
                $closing  = floatval($rs->fields['closingbal']);

                $data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln_prev'] = ($data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                $data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln'] = ($data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln'] ?? 0) + $amt_bln;
                $data_pl[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_pl[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $closing;

                $data_pl[$coatid]['data'][$coaid]['total']['amount_bln_prev'] += $amt_prev;
                $data_pl[$coatid]['data'][$coaid]['total']['amount_bln'] += $amt_bln;
                $data_pl[$coatid]['data'][$coaid]['total']['closingbal'] += $closing;

                $subtotals[$coatid]['branches'][$bc]['amount_bln_prev'] = ($subtotals[$coatid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                $subtotals[$coatid]['branches'][$bc]['amount_bln'] = ($subtotals[$coatid]['branches'][$bc]['amount_bln'] ?? 0) + $amt_bln;
                $subtotals[$coatid]['branches'][$bc]['closingbal'] = ($subtotals[$coatid]['branches'][$bc]['closingbal'] ?? 0) + $closing;

                $subtotals[$coatid]['total']['amount_bln_prev'] = ($subtotals[$coatid]['total']['amount_bln_prev'] ?? 0) + $amt_prev;
                $subtotals[$coatid]['total']['amount_bln'] = ($subtotals[$coatid]['total']['amount_bln'] ?? 0) + $amt_bln;
                $subtotals[$coatid]['total']['closingbal'] = ($subtotals[$coatid]['total']['closingbal'] ?? 0) + $closing;

                $type = $coatid == 4 ? 'income' : 'cost';
                $grand_totals[$type]['branches'][$bc]['amount_bln_prev'] = ($grand_totals[$type]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                $grand_totals[$type]['branches'][$bc]['amount_bln'] = ($grand_totals[$type]['branches'][$bc]['amount_bln'] ?? 0) + $amt_bln;
                $grand_totals[$type]['branches'][$bc]['closingbal'] = ($grand_totals[$type]['branches'][$bc]['closingbal'] ?? 0) + $closing;

                $grand_totals[$type]['total']['amount_bln_prev'] += $amt_prev;
                $grand_totals[$type]['total']['amount_bln'] += $amt_bln;
                $grand_totals[$type]['total']['closingbal'] += $closing;

                $rs->MoveNext();
            }
        }

        $laba_rugi = ['branches' => [], 'total' => ['amount_bln_prev' => 0, 'amount_bln' => 0, 'closingbal' => 0]];
        foreach ($data_cabang as $bc => $cabang)
        {
            $laba_rugi['branches'][$bc]['amount_bln_prev'] = ($grand_totals['income']['branches'][$bc]['amount_bln_prev'] ?? 0) - ($grand_totals['cost']['branches'][$bc]['amount_bln_prev'] ?? 0);
            $laba_rugi['branches'][$bc]['amount_bln'] = ($grand_totals['income']['branches'][$bc]['amount_bln'] ?? 0) - ($grand_totals['cost']['branches'][$bc]['amount_bln'] ?? 0);
            $laba_rugi['branches'][$bc]['closingbal'] = ($grand_totals['income']['branches'][$bc]['closingbal'] ?? 0) - ($grand_totals['cost']['branches'][$bc]['closingbal'] ?? 0);
        }

        $laba_rugi['total']['amount_bln_prev'] = $grand_totals['income']['total']['amount_bln_prev'] - $grand_totals['cost']['total']['amount_bln_prev'];
        $laba_rugi['total']['amount_bln'] = $grand_totals['income']['total']['amount_bln'] - $grand_totals['cost']['total']['amount_bln'];
        $laba_rugi['total']['closingbal'] = $grand_totals['income']['total']['closingbal'] - $grand_totals['cost']['total']['closingbal'];

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        return view('akuntansi_report.laba_rugi.cetak', compact(
            'cabang',
            'sdate',
            'edate',
            'report_month',
            'bln_prev',
            'bln',
            'data',
            'data_pl',
            'data_cabang',
            'subtotals',
            'laba_rugi'
        ));
    } /*}}}*/

    public function cetak_baru ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $tgl_cetak = date('Y-m-d');

        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $pplrid = $rs->fields['pplrid'];

                if ($rs->fields['coatid'] == 5)
                {
                    $amount_bln_prev = $rs->fields['amount_bln_prev'] * -1;
                    $amount_bln = $rs->fields['amount_bln'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $amount_bln_prev = $rs->fields['amount_bln_prev'];
                    $amount_bln = $rs->fields['amount_bln'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pplrid]['branches'][$bc]['amount_bln_prev'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amount_bln_prev;
                $data_db[$pplrid]['branches'][$bc]['amount_bln'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln'] ?? 0) + $amount_bln;
                $data_db[$pplrid]['branches'][$bc]['closingbal'] = ($data_db[$pplrid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pplrid]['total']['amount_bln_prev'] = ($data_db[$pplrid]['total']['amount_bln_prev'] ?? 0) + $amount_bln_prev;
                $data_db[$pplrid]['total']['amount_bln'] = ($data_db[$pplrid]['total']['amount_bln'] ?? 0) + $amount_bln;
                $data_db[$pplrid]['total']['closingbal'] = ($data_db[$pplrid]['total']['closingbal'] ?? 0) + $closingbal;

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos_rekap();

            while (!$rs_pos->EOF)
            {
                $pplrid = $rs_pos->fields['pplrid'];
                $row = $data_db[$pplrid] ?? [];

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                if (trim($nama) == '') $nama = '&nbsp;';
                if ($is_header == 't') $nama = '<b>'.$nama.'</b>';

                $tmp_amounts = [];
                $tot_prev = $row['total']['amount_bln_prev'] ?? 0;
                $tot_bln  = $row['total']['amount_bln'] ?? 0;
                $tot_cls  = $row['total']['closingbal'] ?? 0;

                foreach ($data_cabang as $bc => $cabang)
                {
                    $amt_prev = format_uang($row['branches'][$bc]['amount_bln_prev'] ?? 0, 2);
                    $amt_bln  = format_uang($row['branches'][$bc]['amount_bln'] ?? 0, 2);
                    $amt_cls  = format_uang($row['branches'][$bc]['closingbal'] ?? 0, 2);

                    if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_prev = '';
                        $amt_bln = '';
                        $amt_cls = '';
                    }

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $amt_prev = '<b><u>'.$amt_prev.'</u></b>'; 
                        $amt_bln = '<b><u>'.$amt_bln.'</u></b>'; 
                        $amt_cls = '<b><u>'.$amt_cls.'</u></b>';
                    }

                    $tmp_amounts['branches'][$bc] = [
                        'amount_bln_prev'   => $amt_prev, 
                        'amount_bln'        => $amt_bln, 
                        'closingbal'        => $amt_cls
                    ];
                }

                $amt_tot_prev = format_uang($tot_prev, 2);
                $amt_tot_bln  = format_uang($tot_bln, 2);
                $amt_tot_cls  = format_uang($tot_cls, 2);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_tot_prev = '';
                    $amt_tot_bln = '';
                    $amt_tot_cls = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_tot_prev = '<b><u>'.$amt_tot_prev.'</u></b>'; 
                    $amt_tot_bln = '<b><u>'.$amt_tot_bln.'</u></b>'; 
                    $amt_tot_cls = '<b><u>'.$amt_tot_cls.'</u></b>';
                }

                $tmp_amounts['total'] = [
                    'amount_bln_prev'   => $amt_tot_prev, 
                    'amount_bln'        => $amt_tot_bln, 
                    'closingbal'        => $amt_tot_cls
                ];

                $tmpdata = array();
                $tmpdata['nama_pos'] = $space.$nama;
                $tmpdata['amounts']  = $tmp_amounts;

                $data_pos[$pplrid] = $tmpdata;
                $empty_pos = false;

                if ($rs_pos->fields['parent_pplrid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pplrid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $p = $row['branches'][$bc]['amount_bln_prev'] ?? 0;
                        $b = $row['branches'][$bc]['amount_bln'] ?? 0;
                        $c = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['amount_bln_prev'] = ($data_db[$parent_id]['branches'][$bc]['amount_bln_prev'] ?? 0) + $p;
                        $data_db[$parent_id]['branches'][$bc]['amount_bln'] = ($data_db[$parent_id]['branches'][$bc]['amount_bln'] ?? 0) + $b;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $c;
                    }

                    $data_db[$parent_id]['total']['amount_bln_prev'] = ($data_db[$parent_id]['total']['amount_bln_prev'] ?? 0) + $tot_prev;
                    $data_db[$parent_id]['total']['amount_bln'] = ($data_db[$parent_id]['total']['amount_bln'] ?? 0) + $tot_bln;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_cls;
                }

                $rs_pos->MoveNext();
            }
        }

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        $pos_lainnya = [];
        if (isset($data_db[0]['total']['closingbal']) && $data_db[0]['total']['closingbal'] <> 0)
        {
            $without_mapping = false;

            foreach ($data_cabang as $bc => $cabang)
            {
                $pos_lainnya['branches'][$bc]['amount_bln'] = format_uang($data_db[0]['branches'][$bc]['amount_bln'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['amount_bln_prev'] = format_uang($data_db[0]['branches'][$bc]['amount_bln_prev'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['closingbal'] = format_uang($data_db[0]['branches'][$bc]['closingbal'] ?? 0, 2);
            }

            $pos_lainnya['total']['amount_bln'] = format_uang($data_db[0]['total']['amount_bln'] ?? 0, 2);
            $pos_lainnya['total']['amount_bln_prev'] = format_uang($data_db[0]['total']['amount_bln_prev'] ?? 0, 2);
            $pos_lainnya['total']['closingbal'] = format_uang($data_db[0]['total']['closingbal'] ?? 0, 2);
        }

        return view('akuntansi_report.laba_rugi.cetak_baru', compact(
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'bln_prev',
            'bln',
            'rs_pos',
            'data_pos',
            'empty_pos',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
        ));
    } /*}}}*/

    public function cetak_baru_detail ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $tgl_cetak = date('Y-m-d');

        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $pplid = $rs->fields['pplid'];

                if ($rs->fields['coatid'] == 5)
                {
                    $amount_prev = $rs->fields['amount_bln_prev'] * -1;
                    $amount = $rs->fields['amount_bln'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $amount_prev = $rs->fields['amount_bln_prev'];
                    $amount = $rs->fields['amount_bln'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pplid]['branches'][$bc]['amount_prev'] = ($data_db[$pplid]['branches'][$bc]['amount_prev'] ?? 0) + floatval($amount_prev);
                $data_db[$pplid]['branches'][$bc]['amount'] = ($data_db[$pplid]['branches'][$bc]['amount'] ?? 0) + floatval($amount);
                $data_db[$pplid]['branches'][$bc]['closingbal'] = ($data_db[$pplid]['branches'][$bc]['closingbal'] ?? 0) + floatval($closingbal);

                $data_db[$pplid]['total']['amount_prev'] = ($data_db[$pplid]['total']['amount_prev'] ?? 0) + floatval($amount_prev);
                $data_db[$pplid]['total']['amount'] = ($data_db[$pplid]['total']['amount'] ?? 0) + floatval($amount);
                $data_db[$pplid]['total']['closingbal'] = ($data_db[$pplid]['total']['closingbal'] ?? 0) + floatval($closingbal);

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos();

            while (!$rs_pos->EOF)
            {
                $pplid = $rs_pos->fields['pplid'];
                $row = $data_db[$pplid] ?? [];
                $colrow = "";

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];
                if (trim($nama) == '') $nama = '&nbsp;';

                if ($is_header == 't') $nama = '<b>'.$nama.'</b>';
                else $nama = "<a href=\"javascript:void(0)\" onclick=\"detail_coa(".$rs_pos->fields['pplid'].");\">".$nama."</a>";

                if ($rs_pos->fields['sum_total'] == 't') $colrow = '#F2ECEC';

                $tmp_amounts = [];
                foreach ($data_cabang as $bc => $cabang)
                {
                    $amt      = format_uang($row['branches'][$bc]['amount'] ?? 0, 2);
                    $amt_prev = format_uang($row['branches'][$bc]['amount_prev'] ?? 0, 2);
                    $amt_cls  = format_uang($row['branches'][$bc]['closingbal'] ?? 0, 2);

                    if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt = '';
                        $amt_prev = '';
                        $amt_cls = '';
                    }

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $amt      = '<b><u>'.$amt.'</u></b>';
                        $amt_prev = '<b><u>'.$amt_prev.'</u></b>';
                        $amt_cls  = '<b><u>'.$amt_cls.'</u></b>';
                    }

                    $tmp_amounts['branches'][$bc] = [
                        'amount'        => $amt,
                        'amount_prev'   => $amt_prev,
                        'closingbal'    => $amt_cls
                    ];
                }

                $tot_amt      = format_uang($row['total']['amount'] ?? 0, 2);
                $tot_amt_prev = format_uang($row['total']['amount_prev'] ?? 0, 2);
                $tot_cls      = format_uang($row['total']['closingbal'] ?? 0, 2);

                if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $tot_amt = '';
                    $tot_amt_prev = '';
                    $tot_cls = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $tot_amt      = '<b><u>'.$tot_amt.'</u></b>';
                    $tot_amt_prev = '<b><u>'.$tot_amt_prev.'</u></b>';
                    $tot_cls  = '<b><u>'.$tot_cls.'</u></b>';
                }

                $tmp_amounts['total'] = [
                    'amount'        => $tot_amt,
                    'amount_prev'   => $tot_amt_prev,
                    'closingbal'    => $tot_cls
                ];

                $tmpdata = array();
                $tmpdata['nama_pos']    = $space.$nama;
                $tmpdata['color']       = $colrow;
                $tmpdata['amounts']     = $tmp_amounts;

                $data_pos[$pplid] = $tmpdata;
                $empty_pos = false;

                if ($rs_pos->fields['parent_pplid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pplid'];
                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $p = $row['branches'][$bc]['amount_prev'] ?? 0;
                        $b = $row['branches'][$bc]['amount'] ?? 0;
                        $c = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['amount_prev'] = ($data_db[$parent_id]['branches'][$bc]['amount_prev'] ?? 0) + $p;
                        $data_db[$parent_id]['branches'][$bc]['amount'] = ($data_db[$parent_id]['branches'][$bc]['amount'] ?? 0) + $b;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $c;
                    }

                    $tot_p = $row['total']['amount_prev'] ?? 0;
                    $tot_b = $row['total']['amount'] ?? 0;
                    $tot_c = $row['total']['closingbal'] ?? 0;

                    $data_db[$parent_id]['total']['amount_prev'] = ($data_db[$parent_id]['total']['amount_prev'] ?? 0) + $tot_p;
                    $data_db[$parent_id]['total']['amount'] = ($data_db[$parent_id]['total']['amount'] ?? 0) + $tot_b;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_c;
                }

                $rs_pos->MoveNext();
            }
        }

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        $pos_lainnya = [];
        if (isset($data_db[0]['total']['closingbal']) && $data_db[0]['total']['closingbal'] <> 0)
        {
            $without_mapping = false;
            foreach ($data_cabang as $bc => $cabang)
            {
                $pos_lainnya['branches'][$bc]['amount'] = format_uang($data_db[0]['branches'][$bc]['amount'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['amount_prev'] = format_uang($data_db[0]['branches'][$bc]['amount_prev'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['closingbal'] = format_uang($data_db[0]['branches'][$bc]['closingbal'] ?? 0, 2);
            }

            $pos_lainnya['total']['amount'] = format_uang($data_db[0]['total']['amount'] ?? 0, 2);
            $pos_lainnya['total']['amount_prev'] = format_uang($data_db[0]['total']['amount_prev'] ?? 0, 2);
            $pos_lainnya['total']['closingbal'] = format_uang($data_db[0]['total']['closingbal'] ?? 0, 2);
        }

        return view('akuntansi_report.laba_rugi.cetak_baru_detail', compact(
            'mytipe',
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'bln_prev',
            'bln',
            'rs_pos',
            'data_pos',
            'empty_pos',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
        ));
    } /*}}}*/

    public function detail_coa ($mytipe, $myid) /*{{{*/
    {
        if ($mytipe == 'pl-new-detail-daily') return self::detail_coa_daily($mytipe, $myid);

        $tgl_cetak = date('Y-m-d');

        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month', date('n'))),
            'year'          => get_var('year', date('Y')),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $data_db = [];
        $empty_pos = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                if ($myid == $rs->fields['pplid'])
                {
                    $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                    $coaid = $rs->fields['coaid'];

                    if ($rs->fields['coatid'] == 5)
                    {
                        $amount_bln_prev = $rs->fields['amount_bln_prev'] * -1;
                        $amount_bln = $rs->fields['amount_bln'] * -1;
                        $closingbal = $rs->fields['closingbal'] * -1;
                    }
                    else
                    {
                        $amount_bln_prev = $rs->fields['amount_bln_prev'];
                        $amount_bln = $rs->fields['amount_bln'];
                        $closingbal = $rs->fields['closingbal'];
                    }

                    $empty_pos = false;

                    if (!isset($data_db[$coaid]))
                    {
                        $data_db[$coaid] = [
                            'coa'       => $rs->fields['mycoa'],
                            'branches'  => [],
                            'total'     => [
                                'amount_bln_prev'   => 0,
                                'amount_bln'        => 0,
                                'closingbal'        => 0
                            ]
                        ];
                    }

                    $data_db[$coaid]['branches'][$bc]['amount_bln_prev'] = ($data_db[$coaid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amount_bln_prev;
                    $data_db[$coaid]['branches'][$bc]['amount_bln'] = ($data_db[$coaid]['branches'][$bc]['amount_bln'] ?? 0) + $amount_bln;
                    $data_db[$coaid]['branches'][$bc]['closingbal'] = ($data_db[$coaid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                    $data_db[$coaid]['total']['amount_bln_prev'] += $amount_bln_prev;
                    $data_db[$coaid]['total']['amount_bln'] += $amount_bln;
                    $data_db[$coaid]['total']['closingbal'] += $closingbal;
                }

                $rs->MoveNext();
            }
        }

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        return view('akuntansi_report.laba_rugi.detail_coa', compact(
            'mytipe',
            'myid',
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'bln_prev',
            'bln',
            'data_db',
            'empty_pos',
            'data_cabang'
        ));
    } /*}}}*/

    public function cetak_baru_daily ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');
        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
            $pplrid = $rs->fields['pplrid'];

            $amount_period = $rs->fields['amount_period'];
            $amount_untill = $rs->fields['amount_untill'];

            $data_db[$pplrid]['branches'][$bc]['amount_period'] = ($data_db[$pplrid]['branches'][$bc]['amount_period'] ?? 0) + floatval($amount_period);
            $data_db[$pplrid]['branches'][$bc]['amount_untill'] = ($data_db[$pplrid]['branches'][$bc]['amount_untill'] ?? 0) + floatval($amount_untill);

            $data_db[$pplrid]['total']['amount_period'] = ($data_db[$pplrid]['total']['amount_period'] ?? 0) + floatval($amount_period);
            $data_db[$pplrid]['total']['amount_untill'] = ($data_db[$pplrid]['total']['amount_untill'] ?? 0) + floatval($amount_untill);

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos_rekap();

        while (!$rs_pos->EOF)
        {
            $pplrid = $rs_pos->fields['pplrid'];
            $row = $data_db[$pplrid] ?? [];

            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            if (trim($nama) == '') $nama = '&nbsp;';
            if ($is_header == 't') $nama = '<b>'.$nama.'</b>';

            $tmp_amounts = [];

            foreach ($data_cabang as $bc => $cabang)
            {
                $amt_per = format_uang($row['branches'][$bc]['amount_period'] ?? 0, 2);
                $amt_unt = format_uang($row['branches'][$bc]['amount_untill'] ?? 0, 2);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_per = '';
                    $amt_unt = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_per = '<b><u>'.$amt_per.'</u></b>';
                    $amt_unt = '<b><u>'.$amt_unt.'</u></b>';
                }

                $tmp_amounts['branches'][$bc] = [
                    'amount_period' => $amt_per,
                    'amount_untill' => $amt_unt
                ];
            }

            $tot_per = format_uang($row['total']['amount_period'] ?? 0, 2);
            $tot_unt = format_uang($row['total']['amount_untill'] ?? 0, 2);

            if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
            {
                $tot_per = '';
                $tot_unt = '';
            }

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $tot_per = '<b><u>'.$tot_per.'</u></b>';
                $tot_unt = '<b><u>'.$tot_unt.'</u></b>';
            }

            $tmp_amounts['total'] = [
                'amount_period' => $tot_per,
                'amount_untill' => $tot_unt
            ];

            $tmpdata = array();
            $tmpdata['nama_pos'] = $space.$nama;
            $tmpdata['amounts']  = $tmp_amounts;

            $data_pos[$pplrid] = $tmpdata;
            $empty_pos = false;

            if ($rs_pos->fields['parent_pplrid'] != '')
            {
                $parent_id = $rs_pos->fields['parent_pplrid'];
                foreach ($data_cabang as $bc => $cabang)
                {
                    $p = $row['branches'][$bc]['amount_period'] ?? 0;
                    $u = $row['branches'][$bc]['amount_untill'] ?? 0;

                    $data_db[$parent_id]['branches'][$bc]['amount_period'] = ($data_db[$parent_id]['branches'][$bc]['amount_period'] ?? 0) + $p;
                    $data_db[$parent_id]['branches'][$bc]['amount_untill'] = ($data_db[$parent_id]['branches'][$bc]['amount_untill'] ?? 0) + $u;
                }

                $tot_p = $row['total']['amount_period'] ?? 0;
                $tot_u = $row['total']['amount_untill'] ?? 0;

                $data_db[$parent_id]['total']['amount_period'] = ($data_db[$parent_id]['total']['amount_period'] ?? 0) + $tot_p;
                $data_db[$parent_id]['total']['amount_untill'] = ($data_db[$parent_id]['total']['amount_untill'] ?? 0) + $tot_u;
            }

            $rs_pos->MoveNext();
        }

        $pos_lainnya = [];
        if (isset($data_db[0]['total']['amount_untill']) && $data_db[0]['total']['amount_untill'] <> 0) // Cek until saja karena ini saldo
        {
            $without_mapping = false;
            foreach ($data_cabang as $bc => $cabang)
            {
                $pos_lainnya['branches'][$bc]['amount_period'] = format_uang($data_db[0]['branches'][$bc]['amount_period'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['amount_untill'] = format_uang($data_db[0]['branches'][$bc]['amount_untill'] ?? 0, 2);
            }

            $pos_lainnya['total']['amount_period'] = format_uang($data_db[0]['total']['amount_period'] ?? 0, 2);
            $pos_lainnya['total']['amount_untill'] = format_uang($data_db[0]['total']['amount_untill'] ?? 0, 2);
        }

        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);

        return view('akuntansi_report.laba_rugi.cetak_baru_daily', compact(
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'rs_pos',
            'data_pos',
            'empty_pos',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
        ));
    } /*}}}*/

    public function cetak_baru_detail_daily ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
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

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');

        $rs_pos = $data_pos = $data_db = [];
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
            $pplid = $rs->fields['pplid'];

            $amount_period = floatval($rs->fields['amount_period']);
            $amount_untill = floatval($rs->fields['amount_untill']);

            $data_db[$pplid]['branches'][$bc]['amount_period'] = ($data_db[$pplid]['branches'][$bc]['amount_period'] ?? 0) + $amount_period;
            $data_db[$pplid]['branches'][$bc]['amount_untill'] = ($data_db[$pplid]['branches'][$bc]['amount_untill'] ?? 0) + $amount_untill;

            $data_db[$pplid]['total']['amount_period'] = ($data_db[$pplid]['total']['amount_period'] ?? 0) + $amount_period;
            $data_db[$pplid]['total']['amount_untill'] = ($data_db[$pplid]['total']['amount_untill'] ?? 0) + $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos();

        while (!$rs_pos->EOF)
        {
            $pplid = $rs_pos->fields['pplid'];
            $row = $data_db[$pplid] ?? [];
            $colrow = "";

            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            if (trim($nama) == '') $nama = '&nbsp;';

            if ($is_header == 't') $nama = '<b>'.$nama.'</b>';
            else $nama = "<a href=\"javascript:void(0)\" onclick=\"detail_coa(".$rs_pos->fields['pplid'].");\">".$nama."</a>";

            if ($rs_pos->fields['sum_total'] == 't') $colrow = '#F2ECEC';

            $tmp_amounts = [];
            foreach ($data_cabang as $bc => $cabang)
            {
                $amt_per = format_uang($row['branches'][$bc]['amount_period'] ?? 0, 2);
                $amt_unt = format_uang($row['branches'][$bc]['amount_untill'] ?? 0, 2);

                if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_per = '';
                    $amt_unt = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_per = '<b><u>'.$amt_per.'</u></b>';
                    $amt_unt = '<b><u>'.$amt_unt.'</u></b>';
                }

                $tmp_amounts['branches'][$bc] = [
                    'amount_period' => $amt_per,
                    'amount_untill' => $amt_unt
                ];
            }

            $tot_per = format_uang($row['total']['amount_period'] ?? 0, 2);
            $tot_unt = format_uang($row['total']['amount_untill'] ?? 0, 2);

            if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
            {
                $tot_per = '';
                $tot_unt = '';
            }

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $tot_per = '<b><u>'.$tot_per.'</u></b>';
                $tot_unt = '<b><u>'.$tot_unt.'</u></b>';
            }

            $tmp_amounts['total'] = [
                'amount_period' => $tot_per,
                'amount_untill' => $tot_unt
            ];

            $tmpdata = array();
            $tmpdata['nama_pos']    = $space.$nama;
            $tmpdata['color']       = $colrow;
            $tmpdata['amounts']     = $tmp_amounts;

            $data_pos[$pplid] = $tmpdata;
            $empty_pos = false;

            if ($rs_pos->fields['parent_pplid'] != '')
            {
                $parent_id = $rs_pos->fields['parent_pplid'];

                foreach ($data_cabang as $bc => $cabang)
                {
                    $p = $row['branches'][$bc]['amount_period'] ?? 0;
                    $u = $row['branches'][$bc]['amount_untill'] ?? 0;

                    $data_db[$parent_id]['branches'][$bc]['amount_period'] = ($data_db[$parent_id]['branches'][$bc]['amount_period'] ?? 0) + $p;
                    $data_db[$parent_id]['branches'][$bc]['amount_untill'] = ($data_db[$parent_id]['branches'][$bc]['amount_untill'] ?? 0) + $u;
                }

                $tot_p = $row['total']['amount_period'] ?? 0;
                $tot_u = $row['total']['amount_untill'] ?? 0;

                $data_db[$parent_id]['total']['amount_period'] = ($data_db[$parent_id]['total']['amount_period'] ?? 0) + $tot_p;
                $data_db[$parent_id]['total']['amount_untill'] = ($data_db[$parent_id]['total']['amount_untill'] ?? 0) + $tot_u;
            }

            $rs_pos->MoveNext();
        }

        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);

        $pos_lainnya = [];
        if (isset($data_db[0]['total']['amount_untill']) && $data_db[0]['total']['amount_untill'] <> 0)
        {
            $without_mapping = false;
            foreach ($data_cabang as $bc => $cabang)
            {
                $pos_lainnya['branches'][$bc]['amount_period'] = format_uang($data_db[0]['branches'][$bc]['amount_period'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['amount_untill'] = format_uang($data_db[0]['branches'][$bc]['amount_untill'] ?? 0, 2);
            }

            $pos_lainnya['total']['amount_period'] = format_uang($data_db[0]['total']['amount_period'] ?? 0, 2);
            $pos_lainnya['total']['amount_untill'] = format_uang($data_db[0]['total']['amount_untill'] ?? 0, 2);
        }

        return view('akuntansi_report.laba_rugi.cetak_baru_detail_daily', compact(
            'mytipe',
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'rs_pos',
            'data_pos',
            'empty_pos',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
        ));
    } /*}}}*/

    public function detail_coa_daily ($mytipe, $myid) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');

        $data_db = [];
        $empty_pos = true;

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
            $coaid = $rs->fields['coaid'];

            if ($myid == $rs->fields['pplid'])
            {
                $empty_pos = false;

                $amount_period = floatval($rs->fields['amount_period']);
                $amount_untill = floatval($rs->fields['amount_untill']);

                if (!isset($data_db[$coaid]))
                {
                    $data_db[$coaid] = [
                        'coa'       => $rs->fields['mycoa'],
                        'branches'  => [],
                        'total'     => [
                            'amount_period' => 0,
                            'amount_untill' => 0
                        ]
                    ];
                }

                $data_db[$coaid]['branches'][$bc]['amount_period'] = ($data_db[$coaid]['branches'][$bc]['amount_period'] ?? 0) + $amount_period;
                $data_db[$coaid]['branches'][$bc]['amount_untill'] = ($data_db[$coaid]['branches'][$bc]['amount_untill'] ?? 0) + $amount_untill;

                $data_db[$coaid]['total']['amount_period'] += $amount_period;
                $data_db[$coaid]['total']['amount_untill'] += $amount_untill;
            }

            $rs->MoveNext();
        }

        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);

        return view('akuntansi_report.laba_rugi.detail_coa_daily', compact(
            'mytipe',
            'myid',
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'data_db',
            'empty_pos',
            'data_cabang'
        ));
    } /*}}}*/
}
?>