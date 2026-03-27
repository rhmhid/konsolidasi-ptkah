<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Neraca extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaMdl');
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

        $ho_jkk = dataConfigs('default_kode_branch_jkk');

        // if ($mytipe == 'bs-new') return self::cetak_baru($data);
        // elseif ($mytipe == 'bs-new-detail') return self::cetak_baru_detail($data);

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
                $branch_code = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? $ho_jkk : $rs->fields['branch_code'];

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
                $branch_code = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? $ho_jkk : $rss->fields['branch_code'];

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

        // myprint_r($data_db);
        // myprint_r($data_bs);
        // die();

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

    // public function cetak_baru ($data) /*{{{*/
    // {
    //     $data = array(
    //         'month' => intval(get_var('month')),
    //         'year'  => get_var('year'),
    //     );

    //     $tgl_cetak = date('Y-m-d');

    //     if ($data['month'] > 12)
    //     {
    //         $data['prev_month'] = $data['month'] - 1;
    //         $data['prev_year'] = $data['year'];

    //         $edate = '31'.'-'.$data['month'].'-'.$data['year'];
    //         $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

    //         if ($data['prev_month'] == 12)
    //             $sdate = date("Y-m-t", strtotime($sdate));
    //     }
    //     else
    //     {
    //         $edate = $data['year'].'-'.$data['month'].'-01';
    //         $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

    //         $edate = date("Y-m-t", strtotime($edate));
    //         $sdate = date("Y-m-t", strtotime($sdate));

    //         $data['prev_month'] = date("n", strtotime($sdate));
    //         $data['prev_year'] = date("Y", strtotime($sdate));
    //     }

    //     $rs_pos = $data_pos = [];
    //     $empty_aktiva = $empty_pasiva = $without_mapping = true;

    //     $rs_period = Modules::get_period_akunting($data);

    //     if (!$rs_period->EOF)
    //     {
    //         $data['paid']   = $rs_period->fields['paid'];
    //         $data['pbegin'] = $rs_period->fields['pbegin'];
    //         $data['pend']   = $rs_period->fields['pend'];

    //         $rs = NeracaMdl::list($data);

    //         while (!$rs->EOF)
    //         {
    //             // special untuk acc. akumulasi penyusutan, asset tapi default credet
    //             if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f')
    //             {
    //                 $openingbal = $rs->fields['openingbal'] * -1;
    //                 $closingbal = $rs->fields['closingbal'] * -1;
    //             }
    //             else
    //             {
    //                 $openingbal = $rs->fields['openingbal'];
    //                 $closingbal = $rs->fields['closingbal'];
    //             }

    //             $data_db[$rs->fields['pnid']]['openingbal'] += $openingbal;
    //             $data_db[$rs->fields['pnid']]['closingbal'] += $closingbal;

    //             $rs->MoveNext();
    //         }

    //         $rss = Modules::laba_rugi($data);

    //         while (!$rss->EOF)
    //         {
    //             $data_db[$rss->fields['pnid']]['openingbal'] += $rss->fields['openingbal'];
    //             $data_db[$rss->fields['pnid']]['closingbal'] += $rss->fields['closingbal'];

    //             $rss->MoveNext();
    //         }

    //         $rs_pos = NeracaMdl::list_pos();

    //         while (!$rs_pos->EOF)
    //         {
    //             $row = $data_db[$rs_pos->fields['pnid']];

    //             $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
    //             $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

    //             $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

    //             if (trim($nama) == '') $nama = '&nbsp;';

    //             if ($is_header == 't') $nama = '<b>'.$nama.'</b>';

    //             $amount_prev = format_uang($row['openingbal'], 2);
    //             $amount = format_uang($row['closingbal'], 2);

    //             if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
    //             {
    //                 $amount_prev = '';
    //                 $amount = '';
    //             }

    //             if ($rs_pos->fields['sum_total'] == 't')
    //             {
    //                 $amount_prev = '<b><u>'.$amount_prev.'</u></b>';
    //                 $amount = '<b><u>'.$amount.'</u></b>';
    //             }

    //             $tmpdata = array();
    //             $tmpdata['nama_pos']    = $space.$nama;
    //             $tmpdata['amount_prev'] = $amount_prev;
    //             $tmpdata['amount']      = $amount;

    //             $data_pos[$rs_pos->fields['jenis_pos']][$rs_pos->fields['pnid']] = $tmpdata;

    //             if ($rs_pos->fields['jenis_pos'] == 1) $empty_aktiva = false;
    //             elseif ($rs_pos->fields['jenis_pos'] == 2) $empty_pasiva = false;

    //             // Subtotal Per Header
    //             if ($rs_pos->fields['parent_pnid'] != '')
    //             {
    //                 $data_db[$rs_pos->fields['parent_pnid']]['openingbal'] += $row['openingbal'];
    //                 $data_db[$rs_pos->fields['parent_pnid']]['closingbal'] += $row['closingbal'];
    //             }

    //             $rs_pos->MoveNext();
    //         }
    //     }

    //     if ($data['month'] <= 12)
    //     {
    //         $edate = strtoupper(dbtstamp2stringina($edate));
    //         $bln = monthnamelong($data['month']).' '.$data['year'];
    //     }
    //     else
    //         $bln = $data['month'].'-'.$data['year'];

    //     if ($data['prev_month'] <= 12)
    //     {
    //         $sdate = strtoupper(dbtstamp2stringina($sdate));
    //         $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
    //     }
    //     else
    //         $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

    //     if ($data_db[0]['closingbal'] <> 0)
    //     {
    //         $without_mapping = false;

    //         $pos_amount_prev = format_uang($data_db[0]['openingbal'], 2);
    //         $pos_amount = format_uang($data_db[0]['closingbal'], 2);
    //     }

    //     return view('akuntansi_report.neraca.cetak_baru', compact(
    //         'sdate',
    //         'edate',
    //         'tgl_cetak',
    //         'data',
    //         'bln_prev',
    //         'bln',
    //         'rs_pos',
    //         'data_pos',
    //         'empty_aktiva',
    //         'empty_pasiva',
    //         'without_mapping',
    //         'pos_amount_prev',
    //         'pos_amount'
    //     ));
    // } /*}}}*/

    // public function cetak_baru_detail ($data) /*{{{*/
    // {
    //     $data = array(
    //         'month' => intval(get_var('month')),
    //         'year'  => get_var('year'),
    //     );

    //     $tgl_cetak = date('Y-m-d');

    //     if ($data['month'] > 12)
    //     {
    //         $data['prev_month'] = $data['month'] - 1;
    //         $data['prev_year'] = $data['year'];

    //         $edate = '31'.'-'.$data['month'].'-'.$data['year'];
    //         $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

    //         if ($data['prev_month'] == 12)
    //             $sdate = date("Y-m-t", strtotime($sdate));
    //     }
    //     else
    //     {
    //         $edate = $data['year'].'-'.$data['month'].'-01';
    //         $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

    //         $edate = date("Y-m-t", strtotime($edate));
    //         $sdate = date("Y-m-t", strtotime($sdate));

    //         $data['prev_month'] = date("n", strtotime($sdate));
    //         $data['prev_year'] = date("Y", strtotime($sdate));
    //     }

    //     $rs_pos = $data_pos = [];
    //     $empty_aktiva = $empty_pasiva = $without_mapping = true;

    //     $rs_period = Modules::get_period_akunting($data);

    //     if (!$rs_period->EOF)
    //     {
    //         $data['paid']   = $rs_period->fields['paid'];
    //         $data['pbegin'] = $rs_period->fields['pbegin'];
    //         $data['pend']   = $rs_period->fields['pend'];

    //         $rs = NeracaMdl::list($data);

    //         while (!$rs->EOF)
    //         {
    //             // special untuk acc. akumulasi penyusutan, asset tapi default credet
    //             if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f')
    //             {
    //                 $openingbal = $rs->fields['openingbal'] * -1;
    //                 $closingbal = $rs->fields['closingbal'] * -1;
    //             }
    //             else
    //             {
    //                 $openingbal = $rs->fields['openingbal'];
    //                 $closingbal = $rs->fields['closingbal'];
    //             }

    //             $data_db[$rs->fields['pnid']]['openingbal'] += $openingbal;
    //             $data_db[$rs->fields['pnid']]['closingbal'] += $closingbal;

    //             $tmpdata = array();
    //             $tmpdata['coaid']       = $rs->fields['coaid'];
    //             $tmpdata['coa']         = $rs->fields['coacode'].' '.$rs->fields['coaname'];
    //             $tmpdata['amount_prev'] = $openingbal;
    //             $tmpdata['amount']      = $closingbal;

    //             $data_db[$rs->fields['pnid']]['data'][$rs->fields['coaid']] = $tmpdata;

    //             $rs->MoveNext();
    //         }

    //         $rss = Modules::laba_rugi($data);

    //         while (!$rss->EOF)
    //         {
    //             $data_db[$rss->fields['pnid']]['openingbal'] += $rss->fields['openingbal'];
    //             $data_db[$rss->fields['pnid']]['closingbal'] += $rss->fields['closingbal'];

    //             $tmpdata = array();
    //             $tmpdata['coaid']       = $rss->fields['coaid'];
    //             $tmpdata['coa']         = $rss->fields['coacode'].' '.$rss->fields['coaname'];
    //             $tmpdata['amount_prev'] = $data_db[$rss->fields['pnid']]['data'][$rss->fields['coaid']]['amount_prev'] + $rss->fields['openingbal'];
    //             $tmpdata['amount']      = $data_db[$rss->fields['pnid']]['data'][$rss->fields['coaid']]['amount'] + $rss->fields['closingbal'];

    //             $data_db[$rss->fields['pnid']]['data'][$rss->fields['coaid']] = $tmpdata;

    //             $rss->MoveNext();
    //         }

    //         $rs_pos = NeracaMdl::list_pos();

    //         while (!$rs_pos->EOF)
    //         {
    //             $row = $data_db[$rs_pos->fields['pnid']];
    //             $colrow = "";

    //             $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
    //             $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

    //             $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

    //             if (trim($nama) == '') $nama = '&nbsp;';

    //             $amount_prev = format_uang($row['openingbal'], 2);
    //             $amount = format_uang($row['closingbal'], 2);

    //             if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
    //             {
    //                 $amount_prev = '';
    //                 $amount = '';
    //             }

    //             if ($rs_pos->fields['sum_total'] == 't')
    //             {
    //                 $amount_prev = '<b><u>'.$amount_prev.'</u></b>';
    //                 $amount = '<b><u>'.$amount.'</u></b>';
    //                 $colrow = '#F2ECEC';
    //             }

    //             $tmpdata = array();
    //             $tmpdata['nama_pos']    = $space.'<b>'.$nama.'</b>';
    //             $tmpdata['amount_prev'] = '<b>'.$amount_prev.'</b>';
    //             $tmpdata['amount']      = '<b>'.$amount.'</b>';
    //             $tmpdata['color']       = $colrow;

    //             $data_pos[$rs_pos->fields['jenis_pos']][$rs_pos->fields['pnid']][$rs_pos->fields['pnid']] = $tmpdata;

    //             if (isset($row['data']))
    //             {
    //                 foreach ($row['data'] as $coaid => $val)
    //                 {
    //                     $space2 = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", ($rs_pos->fields['level'] + 1));

    //                     $tmpdata = array();
    //                     $tmpdata['nama_pos']    = $space2.$val['coa'];
    //                     $tmpdata['amount_prev'] = format_uang($val['amount_prev'], 2);
    //                     $tmpdata['amount']      = format_uang($val['amount'], 2);
    //                     $tmpdata['color']       = '';

    //                     $data_pos[$rs_pos->fields['jenis_pos']][$rs_pos->fields['pnid']][$val['coaid']] = $tmpdata;
    //                 }
    //             }

    //             if ($rs_pos->fields['jenis_pos'] == 1) $empty_aktiva = false;
    //             elseif ($rs_pos->fields['jenis_pos'] == 2) $empty_pasiva = false;

    //             // Subtotal Per Header
    //             if ($rs_pos->fields['parent_pnid'] != '')
    //             {
    //                 $data_db[$rs_pos->fields['parent_pnid']]['openingbal'] += $row['openingbal'];
    //                 $data_db[$rs_pos->fields['parent_pnid']]['closingbal'] += $row['closingbal'];
    //             }

    //             $rs_pos->MoveNext();
    //         }
    //     }

    //     if ($data['month'] <= 12)
    //     {
    //         $edate = strtoupper(dbtstamp2stringina($edate));
    //         $bln = monthnamelong($data['month']).' '.$data['year'];
    //     }
    //     else
    //         $bln = $data['month'].'-'.$data['year'];

    //     if ($data['prev_month'] <= 12)
    //     {
    //         $sdate = strtoupper(dbtstamp2stringina($sdate));
    //         $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
    //     }
    //     else
    //         $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

    //     if ($data_db[0]['closingbal'] <> 0)
    //     {
    //         $without_mapping = false;

    //         $pos_amount_prev = format_uang($data_db[0]['openingbal'], 2);
    //         $pos_amount = format_uang($data_db[0]['closingbal'], 2);
    //     }

    //     return view('akuntansi_report.neraca.cetak_baru_detail', compact(
    //         'sdate',
    //         'edate',
    //         'tgl_cetak',
    //         'data',
    //         'bln_prev',
    //         'bln',
    //         'rs_pos',
    //         'data_pos',
    //         'empty_aktiva',
    //         'empty_pasiva',
    //         'without_mapping',
    //         'pos_amount_prev',
    //         'pos_amount'
    //     ));
    // } /*}}}*/
}
?>