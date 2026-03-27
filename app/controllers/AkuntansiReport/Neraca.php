<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Neraca extends BaseController
{
    static $ho_jkk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaMdl');

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        return view('akuntansi_report.neraca.list', compact(
            'cmb_cabang'
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

        if ($mytipe == 'bs-new') return self::cetak_baru($data);
        elseif ($mytipe == 'bs-new-detail') return self::cetak_baru_detail($data);

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $data_db = $data_bs = [];
        $empty_asset = $empty_libility = $empty_equity = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $coatid      = $rs->fields['coatid'];
                $coaid       = $rs->fields['coaid'];
                $branch_code = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];

                $data_db[$coatid]['coatype'] = $rs->fields['coatype'];

                if (!isset($data_db[$coatid]['data'][$coaid]))
                {
                    $data_db[$coatid]['data'][$coaid] = [
                        'coacode'       => $rs->fields['coacode'],
                        'coaname'       => $rs->fields['coaname'],
                        'default_debet' => $rs->fields['default_debet']
                    ];
                }

                $data_db[$coatid]['data'][$coaid]['branch'][$branch_code] = [
                    'openingbal' => $data_db[$coatid]['data'][$coaid]['branch'][$branch_code]['openingbal'] + floatval($rs->fields['openingbal']),
                    'closingbal' => $data_db[$coatid]['data'][$coaid]['branch'][$branch_code]['closingbal'] + floatval($rs->fields['closingbal'])
                ];

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $coatid      = $rss->fields['coatid'];
                $coaid       = $rss->fields['coaid'];
                $branch_code = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];

                $data_db[$coatid]['coatype'] = $rss->fields['coatype'];

                if (!isset($data_db[$coatid]['data'][$coaid]))
                {
                    $data_db[$coatid]['data'][$coaid] = [
                        'coacode'       => $rss->fields['coacode'],
                        'coaname'       => $rss->fields['coaname'],
                        'default_debet' => $rss->fields['default_debet']
                    ];
                }

                $data_db[$coatid]['data'][$coaid]['branch'][$branch_code] = [
                    'openingbal' => $data_db[3]['data'][$coaid]['branch'][$branch_code]['openingbal'] + floatval($rss->fields['openingbal']),
                    'closingbal' => $data_db[3]['data'][$coaid]['branch'][$branch_code]['closingbal'] + floatval($rss->fields['closingbal'])
                ];

                $rss->MoveNext();
            }

            if (!empty($data_db))
            {
                $tot_asset = $tot_libility = $tot_equity = [];

                foreach ($data_cabang as $bc => $info)
                {
                    $tot_asset[$bc] = 0;
                    $tot_libility[$bc] = 0;
                    $tot_equity[$bc] = 0;
                }

                foreach ($data_db as $coatid => $tmp)
                {
                    $nomor = 1;

                    foreach ($tmp['data'] as $k => $val)
                    {
                        $coacode = $val['coacode'];

                        $data_bs[$coatid][$coacode] = array(
                            'nomor'     => $nomor++,
                            'coacode'   => $val['coacode'],
                            'coaname'   => $val['coaname'],
                            'posisi'    => $val['default_debet'] == 't' ? 'Dr' : 'Cr',
                            'branch'    => array()
                        );

                        foreach ($data_cabang as $branch_code => $cabang_info)
                        {
                            $opbal   = isset($val['branch'][$branch_code]) ? floatval($val['branch'][$branch_code]['openingbal']) : 0;
                            $closbal = isset($val['branch'][$branch_code]) ? floatval($val['branch'][$branch_code]['closingbal']) : 0;

                            $data_bs[$coatid][$coacode]['branch'][$branch_code] = array(
                                'opbal'   => $opbal,
                                'closbal' => $closbal
                            );

                            if ($coatid == 1)
                            {
                                $empty_asset = false;

                                // special untuk acc. akumulasi penyusutan, asset tapi default credet
                                if ($val['default_debet'] == 'f') $tot_asset[$branch_code] -= $closbal;
                                else $tot_asset[$branch_code] += $closbal;
                            }

                            if ($coatid == 2)
                            {
                                $empty_libility = false;
                                $tot_libility[$branch_code] += $closbal;
                            }

                            if ($coatid == 3)
                            {
                                $empty_equity = false;
                                $tot_equity[$branch_code] += $closbal;
                            }
                        }
                    }
                }
            }
        }

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        $period = $rs->UserDate($data['pbegin'], 'M Y') .' - '.$rs->UserDate($data['pend'], 'M Y');
        
        $tot_libility_equity = [];

        foreach ($data_cabang as $branch_code => $cabang_info)
        {
            $lib = isset($tot_libility[$branch_code]) ? $tot_libility[$branch_code] : 0;
            $eq  = isset($tot_equity[$branch_code]) ? $tot_equity[$branch_code] : 0;

            $tot_libility_equity[$branch_code] = $lib + $eq;
        }

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        return view('akuntansi_report.neraca.cetak', compact(
            'data_cabang',
            'cabang',
            'data',
            'period',
            'report_month',
            'data_db',
            'data_bs',
            'empty_asset',
            'empty_libility',
            'empty_equity',
            'tot_asset',
            'tot_libility',
            'tot_equity',
            'tot_libility_equity'
        ));
    } /*}}}*/

    public function cetak_baru ($data) /*{{{*/
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
            $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $rs_pos = $data_pos = $data_db = [];
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $rs->fields['branch_code'] ?? '';

                if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f')
                {
                    $openingbal = $rs->fields['openingbal'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $openingbal = $rs->fields['openingbal'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $pnid = $rs->fields['pnid'];

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $closingbal;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $bc = $rss->fields['branch_code'] ?? '';
                $pnid = $rss->fields['pnid'];

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $rss->fields['openingbal'];
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $rss->fields['closingbal'];

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $rss->fields['openingbal'];
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $rss->fields['closingbal'];

                $rss->MoveNext();
            }

            $rs_pos = NeracaMdl::list_pos();

            while (!$rs_pos->EOF)
            {
                $pnid = $rs_pos->fields['pnid'];
                $row = $data_db[$pnid] ?? [];

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                if (trim($nama) == '') $nama = '&nbsp;';
                if ($is_header == 't') $nama = '<b>'.$nama.'</b>';

                $tmp_amounts = [];
                $tot_op = $row['total']['openingbal'] ?? 0;
                $tot_cl = $row['total']['closingbal'] ?? 0;

                foreach ($data_cabang as $bc => $cabang)
                {
                    $op = $row['branches'][$bc]['openingbal'] ?? 0;
                    $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                    $amt_prev = format_uang($op, 2);
                    $amt = format_uang($cl, 2);

                    if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_prev = '';
                        $amt = '';
                    }

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $amt_prev = '<b><u>'.$amt_prev.'</u></b>';
                        $amt = '<b><u>'.$amt.'</u></b>';
                    }

                    $tmp_amounts['branches'][$bc] = [
                        'amount_prev'   => $amt_prev,
                        'amount'        => $amt
                    ];
                }

                $amt_tot_prev = format_uang($tot_op, 2);
                $amt_tot = format_uang($tot_cl, 2);

                if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_tot_prev = '';
                    $amt_tot = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_tot_prev = '<b><u>'.$amt_tot_prev.'</u></b>';
                    $amt_tot = '<b><u>'.$amt_tot.'</u></b>';
                }

                $tmp_amounts['total'] = [
                    'amount_prev'   => $amt_tot_prev,
                    'amount'        => $amt_tot
                ];

                $tmpdata = array();
                $tmpdata['nama_pos'] = $space.$nama;
                $tmpdata['amounts']  = $tmp_amounts;

                $data_pos[$rs_pos->fields['jenis_pos']][$pnid] = $tmpdata;

                if ($rs_pos->fields['jenis_pos'] == 1) $empty_aktiva = false;
                elseif ($rs_pos->fields['jenis_pos'] == 2) $empty_pasiva = false;

                if ($rs_pos->fields['parent_pnid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pnid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $op = $row['branches'][$bc]['openingbal'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['openingbal'] = ($data_db[$parent_id]['branches'][$bc]['openingbal'] ?? 0) + $op;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $cl;
                    }

                    $data_db[$parent_id]['total']['openingbal'] = ($data_db[$parent_id]['total']['openingbal'] ?? 0) + $tot_op;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_cl;
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
                $pos_lainnya['branches'][$bc]['amount_prev']    = format_uang($data_db[0]['branches'][$bc]['openingbal'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['amount']         = format_uang($data_db[0]['branches'][$bc]['closingbal'] ?? 0, 2);
            }

            $pos_lainnya['total']['amount_prev'] = format_uang($data_db[0]['total']['openingbal'] ?? 0, 2);
            $pos_lainnya['total']['amount'] = format_uang($data_db[0]['total']['closingbal'] ?? 0, 2);
        }

        return view('akuntansi_report.neraca.cetak_baru', compact(
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'bln_prev',
            'bln',
            'rs_pos',
            'data_pos',
            'empty_aktiva',
            'empty_pasiva',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
        ));
    } /*}}}*/

    public function cetak_baru_detail ($data) /*{{{*/
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
            $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $rs_pos = $data_pos = $data_db = [];
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $rs->fields['branch_code'] ?? '';
                $pnid = $rs->fields['pnid'];
                $coaid = $rs->fields['coaid'];

                if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f')
                {
                    $openingbal = $rs->fields['openingbal'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $openingbal = $rs->fields['openingbal'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['data'][$coaid]['coa'] = $rs->fields['coacode'].' '.$rs->fields['coaname'];

                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['data'][$coaid]['total']['openingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['data'][$coaid]['total']['closingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['closingbal'] ?? 0) + $closingbal;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $bc = $rss->fields['branch_code'] ?? '';
                $pnid = $rss->fields['pnid'];
                $coaid = $rss->fields['coaid'];
                $op = $rss->fields['openingbal'];
                $cl = $rss->fields['closingbal'];

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['data'][$coaid]['coa'] = $rss->fields['coacode'].' '.$rss->fields['coaname'];

                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['data'][$coaid]['total']['openingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['data'][$coaid]['total']['closingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['closingbal'] ?? 0) + $cl;

                $rss->MoveNext();
            }

            $rs_pos = NeracaMdl::list_pos();

            while (!$rs_pos->EOF)
            {
                $pnid = $rs_pos->fields['pnid'];
                $row = $data_db[$pnid] ?? [];
                $colrow = "";

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                if (trim($nama) == '') $nama = '&nbsp;';

                if ($rs_pos->fields['sum_total'] == 't') $colrow = '#F2ECEC';

                $tot_op = $row['total']['openingbal'] ?? 0;
                $tot_cl = $row['total']['closingbal'] ?? 0;

                $tmp_amounts = [];

                foreach ($data_cabang as $bc => $cabang)
                {
                    $op = $row['branches'][$bc]['openingbal'] ?? 0;
                    $cl = $row['branches'][$bc]['closingbal'] ?? 0;
                    $amt_prev = format_uang($op, 2);
                    $amt = format_uang($cl, 2);

                    if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_prev = '';
                        $amt = '';
                    }

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $amt_prev = '<b><u>'.$amt_prev.'</u></b>';
                        $amt = '<b><u>'.$amt.'</u></b>';
                    }

                    $tmp_amounts['branches'][$bc] = [
                        'amount_prev'   => '<b>'.$amt_prev.'</b>',
                        'amount'        => '<b>'.$amt.'</b>'
                    ];
                }

                $amt_tot_prev = format_uang($tot_op, 2);
                $amt_tot = format_uang($tot_cl, 2);

                if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_tot_prev = '';
                    $amt_tot = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_tot_prev = '<b><u>'.$amt_tot_prev.'</u></b>';
                    $amt_tot = '<b><u>'.$amt_tot.'</u></b>';
                }

                $tmp_amounts['total'] = [
                    'amount_prev'   => '<b>'.$amt_tot_prev.'</b>',
                    'amount'        => '<b>'.$amt_tot.'</b>'
                ];

                $tmpdata = array();
                $tmpdata['nama_pos']    = $space.'<b>'.$nama.'</b>';
                $tmpdata['amounts']     = $tmp_amounts;
                $tmpdata['color']       = $colrow;

                $data_pos[$rs_pos->fields['jenis_pos']][$pnid][$pnid] = $tmpdata;

                if (isset($row['data']))
                {
                    foreach ($row['data'] as $coaid => $val)
                    {
                        $space2 = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", ($rs_pos->fields['level'] + 1));
                        $tmp_amounts_detail = [];

                        foreach ($data_cabang as $bc => $cabang)
                        {
                            $op_d = $val['branches'][$bc]['openingbal'] ?? 0;
                            $cl_d = $val['branches'][$bc]['closingbal'] ?? 0;

                            $tmp_amounts_detail['branches'][$bc] = [
                                'amount_prev'   => format_uang($op_d, 2),
                                'amount'        => format_uang($cl_d, 2)
                            ];
                        }

                        $tot_op_d = $val['total']['openingbal'] ?? 0;
                        $tot_cl_d = $val['total']['closingbal'] ?? 0;

                        $tmp_amounts_detail['total'] = [
                            'amount_prev'   => format_uang($tot_op_d, 2),
                            'amount'        => format_uang($tot_cl_d, 2)
                        ];

                        $tmpdata = array();
                        $tmpdata['nama_pos']    = $space2.$val['coa'];
                        $tmpdata['amounts']     = $tmp_amounts_detail;
                        $tmpdata['color']       = '';

                        $data_pos[$rs_pos->fields['jenis_pos']][$pnid][$coaid] = $tmpdata;
                    }
                }

                if ($rs_pos->fields['jenis_pos'] == 1) $empty_aktiva = false;
                elseif ($rs_pos->fields['jenis_pos'] == 2) $empty_pasiva = false;

                if ($rs_pos->fields['parent_pnid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pnid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $op = $row['branches'][$bc]['openingbal'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['openingbal'] = ($data_db[$parent_id]['branches'][$bc]['openingbal'] ?? 0) + $op;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $cl;
                    }

                    $data_db[$parent_id]['total']['openingbal'] = ($data_db[$parent_id]['total']['openingbal'] ?? 0) + $tot_op;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_cl;
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
                $pos_lainnya['branches'][$bc]['amount_prev']    = format_uang($data_db[0]['branches'][$bc]['openingbal'] ?? 0, 2);
                $pos_lainnya['branches'][$bc]['amount']         = format_uang($data_db[0]['branches'][$bc]['closingbal'] ?? 0, 2);
            }
            $pos_lainnya['total']['amount_prev']    = format_uang($data_db[0]['total']['openingbal'] ?? 0, 2);
            $pos_lainnya['total']['amount']         = format_uang($data_db[0]['total']['closingbal'] ?? 0, 2);
        }

        return view('akuntansi_report.neraca.cetak_baru_detail', compact(
            'sdate',
            'edate',
            'tgl_cetak',
            'data',
            'bln_prev',
            'bln',
            'rs_pos',
            'data_pos',
            'empty_aktiva',
            'empty_pasiva',
            'without_mapping',
            'pos_lainnya',
            'data_cabang'
        ));
    } /*}}}*/
}
?>