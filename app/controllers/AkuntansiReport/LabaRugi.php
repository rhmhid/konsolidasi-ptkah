<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class LabaRugi extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/LabaRugiMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        return view('akuntansi_report.laba_rugi.list', compact(
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

        if ($mytipe == 'pl-new') return self::cetak_baru($mytipe, $data);
        elseif ($mytipe == 'pl-new-daily') return self::cetak_baru_daily($mytipe, $data);
        elseif ($mytipe == 'pl-new-detail') return self::cetak_baru_detail($mytipe, $data);
        elseif ($mytipe == 'pl-new-detail-daily') return self::cetak_baru_detail_daily($mytipe, $data);

        $coacode_last = $subtot_header = [];

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

        $data_pl = $coacode_last = [];

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $data_pl[] = array(
                    'no'                => $nomor,
                    'coatid'            => $rs->fields['coatid'],
                    'coacode'           => $rs->fields['coacode'],
                    'coaname'           => $rs->fields['coaname'],
                    'posisi'            => $rs->fields['default_debet'] == 't' ? 'Dr' : 'Cr',
                    'amount_bln_prev'   => $rs->fields['amount_bln_prev'],
                    'amount_bln'        => $rs->fields['amount_bln'],
                    'closingbal'        => $rs->fields['closingbal'],
                );

                $coacode_last[$rs->fields['coatid']] = $rs->fields['coacode'];

                $rs->MoveNext();
            }
        }

        $nomor = 1;

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
            'nomor',
            'sdate',
            'edate',
            'report_month',
            'bln_prev',
            'bln',
            'data',
            'data_pl',
            'coacode_last'
        ));
    } /*}}}*/

    public function cetak_baru ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

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

        $rs_pos = $data_pos = [];
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

                $data_db[$rs->fields['pplrid']]['amount_bln_prev'] += $amount_bln_prev;
                $data_db[$rs->fields['pplrid']]['amount_bln'] += $amount_bln;
                $data_db[$rs->fields['pplrid']]['closingbal'] += $closingbal;

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos_rekap();

            while (!$rs_pos->EOF)
            {
                $row = $data_db[$rs_pos->fields['pplrid']];

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                if (trim($nama) == '') $nama = '&nbsp;';

                if ($is_header == 't') $nama = '<b>'.$nama.'</b>';

                $amount_bln_prev = format_uang($row['amount_bln_prev'], 2);
                $amount_bln = format_uang($row['amount_bln'], 2);
                $closingbal = format_uang($row['closingbal'], 2);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amount_bln_prev = '';
                    $amount_bln = '';
                    $closingbal = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amount_bln_prev = '<b><u>'.$amount_bln_prev.'</u></b>';
                    $amount_bln = '<b><u>'.$amount_bln.'</u></b>';
                    $closingbal = '<b><u>'.$closingbal.'</u></b>';
                }

                $tmpdata = array();
                $tmpdata['nama_pos']        = $space.$nama;
                $tmpdata['amount_bln_prev'] = $amount_bln_prev;
                $tmpdata['amount_bln']      = $amount_bln;
                $tmpdata['closingbal']      = $closingbal;

                $data_pos[$rs_pos->fields['pplrid']] = $tmpdata;
                $empty_pos = false;

                // Subtotal Per Header
                if ($rs_pos->fields['parent_pplrid'] != '')
                {
                    $data_db[$rs_pos->fields['parent_pplrid']]['amount_bln_prev'] += $row['amount_bln_prev'];
                    $data_db[$rs_pos->fields['parent_pplrid']]['amount_bln'] += $row['amount_bln'];
                    $data_db[$rs_pos->fields['parent_pplrid']]['closingbal'] += $row['closingbal'];
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

        if ($data_db[0]['closingbal'] <> 0)
        {
            $without_mapping = false;

            $pos_amount_prev = format_uang($data_db[0]['amount_bln_prev'], 2);
            $pos_amount = format_uang($data_db[0]['amount_bln'], 2);
            $pos_closingbal = format_uang($data_db[0]['closingbal'], 2);
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
            'pos_amount_prev',
            'pos_amount',
            'pos_closingbal'
        ));
    } /*}}}*/

    public function cetak_baru_detail ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

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

        $rs_pos = $data_pos = [];
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
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

                $data_db[$rs->fields['pplid']]['amount_prev'] += $amount_prev;
                $data_db[$rs->fields['pplid']]['amount'] += $amount;
                $data_db[$rs->fields['pplid']]['closingbal'] += $closingbal;

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos();

            while (!$rs_pos->EOF)
            {
                $row = $data_db[$rs_pos->fields['pplid']];
                $colrow = "";

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                if (trim($nama) == '') $nama = '&nbsp;';

                if ($is_header == 't') $nama = '<b>'.$nama.'</b>';
                else $nama = "<a href=\"javascript:void(0)\" onclick=\"detail_coa(".$rs_pos->fields['pplid'].");\">".$nama."</a>";

                $amount_prev = format_uang($row['amount_prev'], 2);
                $amount = format_uang($row['amount'], 2);
                $closingbal = format_uang($row['closingbal'], 2);

                if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amount_prev = '';
                    $amount = '';
                    $closingbal = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amount_prev = '<b><u>'.$amount_prev.'</u></b>';
                    $amount = '<b><u>'.$amount.'</u></b>';
                    $closingbal = '<b><u>'.$closingbal.'</u></b>';
                    $colrow = '#F2ECEC';
                }

                $tmpdata = array();
                $tmpdata['nama_pos']    = $space.$nama;
                $tmpdata['amount_prev'] = $amount_prev;
                $tmpdata['amount']      = $amount;
                $tmpdata['closingbal']  = $closingbal;
                $tmpdata['color']       = $colrow;

                $data_pos[$rs_pos->fields['pplid']] = $tmpdata;

                $empty_pos = false;

                // Subtotal Per Header
                if ($rs_pos->fields['parent_pplid'] != '')
                {
                    $data_db[$rs_pos->fields['parent_pplid']]['amount_prev'] += $row['amount_prev'];
                    $data_db[$rs_pos->fields['parent_pplid']]['amount'] += $row['amount'];
                    $data_db[$rs_pos->fields['parent_pplid']]['closingbal'] += $row['closingbal'];
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

        if ($data_db[0]['closingbal'] <> 0)
        {
            $without_mapping = false;

            $pos_amount_prev = format_uang($data_db[0]['amount_prev'], 2);
            $pos_amount = format_uang($data_db[0]['amount'], 2);
            $pos_closingbal = format_uang($data_db[0]['closingbal'], 2);
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
            'pos_amount_prev',
            'pos_amount',
            'pos_closingbal'
        ));
    } /*}}}*/

    public function detail_coa ($mytipe, $myid) /*{{{*/
    {
        if ($mytipe == 'pl-new-detail-daily') return self::detail_coa_daily($mytipe, $myid);

        $tgl_cetak = date('Y-m-d');

        $data = array(
            'month' => intval(get_var('month', date('n'))),
            'year'  => get_var('year', date('Y')),
        );

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

                if ($myid == $rs->fields['pplid'])
                {
                    $empty_pos = false;

                    $tmpdata = array();
                    $tmpdata['coa']             = $rs->fields['mycoa'];
                    $tmpdata['amount_bln_prev'] = $amount_bln_prev;
                    $tmpdata['amount_bln']      = $amount_bln;
                    $tmpdata['closingbal']      = $closingbal;

                    $data_db[$rs->fields['coaid']] = $tmpdata;
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
            'empty_pos'
        ));
    } /*}}}*/

    public function cetak_baru_daily ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');
        $rs_pos = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            /*if ($rs->fields['coatid'] == 5)
            {
                $amount_period = $rs->fields['amount_period'] * -1;
                $amount_untill = $rs->fields['amount_untill'] * -1;
            }
            else
            {*/
                $amount_period = $rs->fields['amount_period'];
                $amount_untill = $rs->fields['amount_untill'];
            // }

            $data_db[$rs->fields['pplrid']]['amount_period'] += $amount_period;
            $data_db[$rs->fields['pplrid']]['amount_untill'] += $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos_rekap();

        while (!$rs_pos->EOF)
        {
            $row = $data_db[$rs_pos->fields['pplrid']];

            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            if (trim($nama) == '') $nama = '&nbsp;';

            if ($is_header == 't') $nama = '<b>'.$nama.'</b>';

            $amount_period = format_uang($row['amount_period'], 2);
            $amount_untill = format_uang($row['amount_untill'], 2);

            if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
            {
                $amount_period = '';
                $amount_untill = '';
            }

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $amount_period = '<b><u>'.$amount_period.'</u></b>';
                $amount_untill = '<b><u>'.$amount_untill.'</u></b>';
            }

            $tmpdata = array();
            $tmpdata['nama_pos']        = $space.$nama;
            $tmpdata['amount_period']   = $amount_period;
            $tmpdata['amount_untill']   = $amount_untill;

            $data_pos[$rs_pos->fields['pplrid']] = $tmpdata;
            $empty_pos = false;

            // Subtotal Per Header
            if ($rs_pos->fields['parent_pplrid'] != '')
            {
                $data_db[$rs_pos->fields['parent_pplrid']]['amount_period'] += $row['amount_period'];
                $data_db[$rs_pos->fields['parent_pplrid']]['amount_untill'] += $row['amount_untill'];
            }

            $rs_pos->MoveNext();
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $without_mapping = false;

            $pos_amount_period = format_uang($data_db[0]['amount_period'], 2);
            $pos_amount_untill = format_uang($data_db[0]['amount_untill'], 2);
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
            'pos_amount_period',
            'pos_amount_untill'
        ));
    } /*}}}*/

    public function cetak_baru_detail_daily ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');

        $rs_pos = $data_pos = [];
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            /*if ($rs->fields['coatid'] == 5)
            {
                $amount_period = $rs->fields['amount_period'] * -1;
                $amount_untill = $rs->fields['amount_untill'] * -1;
            }
            else
            {*/
                $amount_period = $rs->fields['amount_period'];
                $amount_untill = $rs->fields['amount_untill'];
            // }

            $data_db[$rs->fields['pplid']]['amount_period'] += $amount_period;
            $data_db[$rs->fields['pplid']]['amount_untill'] += $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos();

        while (!$rs_pos->EOF)
        {
            $row = $data_db[$rs_pos->fields['pplid']];
            $colrow = "";

            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            if (trim($nama) == '') $nama = '&nbsp;';

            if ($is_header == 't') $nama = '<b>'.$nama.'</b>';
            else $nama = "<a href=\"javascript:void(0)\" onclick=\"detail_coa(".$rs_pos->fields['pplid'].");\">".$nama."</a>";

            $amount_period = format_uang($row['amount_period'], 2);
            $amount_untill = format_uang($row['amount_untill'], 2);

            if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
            {
                $amount_period = '';
                $amount_untill = '';
            }

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $amount_period = '<b><u>'.$amount_period.'</u></b>';
                $amount_untill = '<b><u>'.$amount_untill.'</u></b>';
                $colrow = '#F2ECEC';
            }

            $tmpdata = array();
            $tmpdata['nama_pos']        = $space.$nama;
            $tmpdata['amount_period']   = $amount_period;
            $tmpdata['amount_untill']   = $amount_untill;
            $tmpdata['color']           = $colrow;

            $data_pos[$rs_pos->fields['pplid']] = $tmpdata;

            $empty_pos = false;

            // Subtotal Per Header
            if ($rs_pos->fields['parent_pplid'] != '')
            {
                $data_db[$rs_pos->fields['parent_pplid']]['amount_period'] += $row['amount_period'];
                $data_db[$rs_pos->fields['parent_pplid']]['amount_untill'] += $row['amount_untill'];
            }

            $rs_pos->MoveNext();
        }

        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);

        if ($data_db[0]['closingbal'] <> 0)
        {
            $without_mapping = false;

            $pos_amount_period = format_uang($data_db[0]['amount_period'], 2);
            $pos_amount_untill = format_uang($data_db[0]['amount_untill'], 2);
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
            'pos_amount_period',
            'pos_amount_untill'
        ));
    } /*}}}*/

    public function detail_coa_daily ($mytipe, $myid) /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');

        $data_db = [];
        $empty_pos = true;

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            /*if ($rs->fields['coatid'] == 5)
            {
                $amount_period = $rs->fields['amount_period'] * -1;
                $amount_untill = $rs->fields['amount_untill'] * -1;
            }
            else
            {*/
                $amount_period = $rs->fields['amount_period'];
                $amount_untill = $rs->fields['amount_untill'];
            // }

            if ($myid == $rs->fields['pplid'])
            {
                $empty_pos = false;

                $tmpdata = array();
                $tmpdata['coa']             = $rs->fields['mycoa'];
                $tmpdata['amount_period']   = $amount_period;
                $tmpdata['amount_untill']   = $amount_untill;

                $data_db[$rs->fields['coaid']] = $tmpdata;
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
            'empty_pos'
        ));
    } /*}}}*/
}
?>