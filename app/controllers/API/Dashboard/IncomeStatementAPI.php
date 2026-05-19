<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class IncomeStatementAPI extends BaseAPIController
{
    static $ho_jkk, $ho_kah;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model(array('AkuntansiReport/LabaRugiMdl', 'Dashboard/DashboardMdl'));

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');

        self::$ho_kah = dataConfigs('default_kode_branch_kah');
    } /*}}}*/

    public function data_get () /*{{{*/
    {
        $data = array(
            'bid'   => get_var('bid'),
            'month' => intval(get_var('month', date('n'))),
            'year'  => intval(get_var('year', date('Y'))),
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
            $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        $rs_period = Modules::get_period_akunting($data);

        $data_pos = $data_db = [];
        $api_data_summary = $api_data_diff = $api_data_komposisi = $api_data_tren_margin = [];
        $api_data_revenue_cabang = $api_data_detail_revenue_cabang = [];

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs_now = LabaRugiMdl::list($data);

            $data_now = $rs_now->GetArray();

            $data2 = $data;

            $data2['year'] = $data2['year_prev'] = ($data['year'] - 1);

            $rs_before = LabaRugiMdl::list($data2);

            $data_before = $rs_before->GetArray();

            $rs = DashboardMdl::list($data_now, $data_before, $data['year']);

            while (!$rs->EOF)
            {
                // $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                if ($data['bid'] == -1 && $rs->fields['kdbid'] == 2) $bc = self::$ho_jkk;
                elseif ($data['bid'] == -1 && $rs->fields['kdbid'] == 3) $bc = self::$ho_kah;
                else $bc = $rs->fields['branch_code'];

                $pplrid = $rs->fields['pplrid'];

                if ($rs->fields['coatid'] == 5)
                {
                    $amount_bln_prev = $rs->fields['amount_bln_prev'] * -1;
                    $amount_bln_prev_before = $rs->fields['amount_bln_prev_before'] * -1;

                    $amount_bln = $rs->fields['amount_bln'] * -1;
                    $amount_bln_before = $rs->fields['amount_bln_before'] * -1;

                    $closingbal = $rs->fields['closingbal'] * -1;
                    $closingbal_before = $rs->fields['closingbal_before'] * -1;
                }
                else
                {
                    $amount_bln_prev = $rs->fields['amount_bln_prev'];
                    $amount_bln_prev_before = $rs->fields['amount_bln_prev_before'];

                    $amount_bln = $rs->fields['amount_bln'];
                    $amount_bln_before = $rs->fields['amount_bln_before'];

                    $closingbal = $rs->fields['closingbal'];
                    $closingbal_before = $rs->fields['closingbal_before'];
                }

                $data_db[$pplrid]['branches'][$bc]['amount_bln_prev'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amount_bln_prev;
                $data_db[$pplrid]['branches'][$bc]['amount_bln_prev_before'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln_prev_before'] ?? 0) + $amount_bln_prev_before;

                $data_db[$pplrid]['branches'][$bc]['amount_bln'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln'] ?? 0) + $amount_bln;
                $data_db[$pplrid]['branches'][$bc]['amount_bln_before'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln_before'] ?? 0) + $amount_bln_before;

                $data_db[$pplrid]['branches'][$bc]['closingbal'] = ($data_db[$pplrid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;
                $data_db[$pplrid]['branches'][$bc]['closingbal_before'] = ($data_db[$pplrid]['branches'][$bc]['closingbal_before'] ?? 0) + $closingbal_before;

                $data_db[$pplrid]['total']['amount_bln_prev'] = ($data_db[$pplrid]['total']['amount_bln_prev'] ?? 0) + $amount_bln_prev;
                $data_db[$pplrid]['total']['amount_bln_prev_before'] = ($data_db[$pplrid]['total']['amount_bln_prev_before'] ?? 0) + $amount_bln_prev_before;

                $data_db[$pplrid]['total']['amount_bln'] = ($data_db[$pplrid]['total']['amount_bln'] ?? 0) + $amount_bln;
                $data_db[$pplrid]['total']['amount_bln_before'] = ($data_db[$pplrid]['total']['amount_bln_before'] ?? 0) + $amount_bln_before;

                $data_db[$pplrid]['total']['closingbal'] = ($data_db[$pplrid]['total']['closingbal'] ?? 0) + $closingbal;
                $data_db[$pplrid]['total']['closingbal_before'] = ($data_db[$pplrid]['total']['closingbal_before'] ?? 0) + $closingbal_before;

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos_rekap();

            while (!$rs_pos->EOF)
            {
                $pplrid = $rs_pos->fields['pplrid'];
                $row = $data_db[$pplrid] ?? [];

                $tmp_amounts = [];
                $tot_bln_prev         = $row['total']['amount_bln_prev'] ?? 0;
                $tot_bln_prev_before  = $row['total']['amount_bln_prev_before'] ?? 0;

                $tot_bln         = $row['total']['amount_bln'] ?? 0;
                $tot_bln_before  = $row['total']['amount_bln_before'] ?? 0;

                $tot_cls         = $row['total']['closingbal'] ?? 0;
                $tot_cls_before  = $row['total']['closingbal_before'] ?? 0;

                foreach ($data_cabang as $bc => $cabang)
                {
                    $amt_bln_prev        = format_uang($row['branches'][$bc]['amount_bln_prev'] ?? 0, 2);
                    $amt_bln_prev_before = format_uang($row['branches'][$bc]['amount_bln_prev_before'] ?? 0, 2);

                    $amt_bln        = format_uang($row['branches'][$bc]['amount_bln'] ?? 0, 2);
                    $amt_bln_before = format_uang($row['branches'][$bc]['amount_bln_before'] ?? 0, 2);

                    $cls        = format_uang($row['branches'][$bc]['closingbal'] ?? 0, 2);
                    $cls_before = format_uang($row['branches'][$bc]['closingbal_before'] ?? 0, 2);

                    if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_bln_prev = '';
                        $amt_bln_prev_before = '';

                        $amt_bln = '';
                        $amt_bln_before = '';

                        $cls = '';
                        $cls_before = '';
                    }

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $amt_bln_prev = '<b><u>'.$amt_bln_prev.'</u></b>'; 
                        $amt_bln_prev_before = '<b><u>'.$amt_bln_prev_before.'</u></b>';

                        $amt_bln = '<b><u>'.$amt_bln.'</u></b>'; 
                        $amt_bln_before = '<b><u>'.$amt_bln_before.'</u></b>';

                        $cls = '<b><u>'.$cls.'</u></b>'; 
                        $cls_before = '<b><u>'.$cls_before.'</u></b>';
                    }

                    $tmp_amounts['branches'][$bc] = [
                        'amount_bln_prev'           => $amt_bln_prev, 
                        'amount_bln_prev_before'    => $amt_bln_prev_before,
                        'amount_bln'                => $amt_bln, 
                        'amount_bln_before'         => $amt_bln_before,
                        'closingbal'                => $cls, 
                        'closingbal_before'         => $cls_before
                    ];
                }

                $amt_tot_bln_prev        = format_uang($tot_bln_prev, 2);
                $amt_tot_bln_prev_before = format_uang($tot_bln_prev_before, 2);

                $amt_tot_bln        = format_uang($tot_bln, 2);
                $amt_tot_bln_before = format_uang($tot_bln_before, 2);

                $cls_tot        = format_uang($tot_cls, 2);
                $cls_tot_before = format_uang($tot_cls_before, 2);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_tot_bln_prev = '';
                    $amt_tot_bln_prev_before = '';

                    $amt_tot_bln = '';
                    $amt_tot_bln_before = '';

                    $cls_tot = '';
                    $cls_tot_before = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_tot_bln_prev = '<b><u>'.$amt_tot_bln_prev.'</u></b>'; 
                    $amt_tot_bln_prev_before = '<b><u>'.$amt_tot_bln_prev_before.'</u></b>';

                    $amt_tot_bln = '<b><u>'.$amt_tot_bln.'</u></b>'; 
                    $amt_tot_bln_before = '<b><u>'.$amt_tot_bln_before.'</u></b>';

                    $cls_tot = '<b><u>'.$cls_tot.'</u></b>'; 
                    $cls_tot_before = '<b><u>'.$cls_tot_before.'</u></b>';
                }

                $tmp_amounts['total'] = [
                    'amount_bln_prev'           => $amt_tot_bln_prev, 
                    'amount_bln_prev_before'    => $amt_tot_bln_prev_before,
                    'amount_bln'                => $amt_tot_bln, 
                    'amount_bln_before'         => $amt_tot_bln_before,
                    'closingbal'                => $cls_tot, 
                    'closingbal_before'         => $cls_tot_before,
                ];

                $tmpdata = array();
                $tmpdata['amounts']  = $tmp_amounts;

                $data_pos[$pplrid] = $tmpdata;

                if ($rs_pos->fields['parent_pplrid'] != '')
                {
                    $parent_pplrid = $rs_pos->fields['parent_pplrid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $bp = $row['branches'][$bc]['amount_bln_prev'] ?? 0;
                        $bpf = $row['branches'][$bc]['amount_bln_prev_before'] ?? 0;

                        $b = $row['branches'][$bc]['amount_bln'] ?? 0;
                        $bf = $row['branches'][$bc]['amount_bln_before'] ?? 0;

                        $bcc = $row['branches'][$bc]['closingbal'] ?? 0;
                        $bcf = $row['branches'][$bc]['closingbal_before'] ?? 0;

                        $data_db[$parent_pplrid]['branches'][$bc]['amount_bln_prev'] = ($data_db[$parent_pplrid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $bp;
                        $data_db[$parent_pplrid]['branches'][$bc]['amount_bln_prev_before'] = ($data_db[$parent_pplrid]['branches'][$bc]['amount_bln_prev_before'] ?? 0) + $bpf;

                        $data_db[$parent_pplrid]['branches'][$bc]['amount_bln'] = ($data_db[$parent_pplrid]['branches'][$bc]['amount_bln'] ?? 0) + $b;
                        $data_db[$parent_pplrid]['branches'][$bc]['amount_bln_before'] = ($data_db[$parent_pplrid]['branches'][$bc]['amount_bln_before'] ?? 0) + $bf;

                        $data_db[$parent_pplrid]['branches'][$bc]['closingbal'] = ($data_db[$parent_pplrid]['branches'][$bc]['closingbal'] ?? 0) + $bcc;
                        $data_db[$parent_pplrid]['branches'][$bc]['closingbal_before'] = ($data_db[$parent_pplrid]['branches'][$bc]['closingbal_before'] ?? 0) + $bcf;
                    }

                    $data_db[$parent_pplrid]['total']['amount_bln_prev']         = ($data_db[$parent_pplrid]['total']['amount_bln_prev'] ?? 0) + $tot_bln_prev;
                    $data_db[$parent_pplrid]['total']['amount_bln_prev_before']  = ($data_db[$parent_pplrid]['total']['amount_bln_prev_before'] ?? 0) + $tot_bln_prev_before;

                    $data_db[$parent_pplrid]['total']['amount_bln']         = ($data_db[$parent_pplrid]['total']['amount_bln'] ?? 0) + $tot_bln;
                    $data_db[$parent_pplrid]['total']['amount_bln_before']  = ($data_db[$parent_pplrid]['total']['amount_bln_before'] ?? 0) + $tot_bln_before;

                    $data_db[$parent_pplrid]['total']['closingbal']         = ($data_db[$parent_pplrid]['total']['closingbal'] ?? 0) + $tot_cls;
                    $data_db[$parent_pplrid]['total']['closingbal_before']  = ($data_db[$parent_pplrid]['total']['closingbal_before'] ?? 0) + $tot_cls_before;
                }

                // B: Total Pendapatan
                $tot_income_yoy_month_prev = $data_db[14]['total']['amount_bln_prev_before'];
                $tot_income_yoy_month_curr = $data_db[14]['total']['amount_bln_prev'];

                $tot_income_yoy_prev = $data_db[14]['total']['amount_bln_before'];
                $tot_income_yoy_curr = $data_db[14]['total']['amount_bln'];

                $tot_income_ytd_prev = $data_db[14]['total']['closingbal_before'];
                $tot_income_ytd_curr = $data_db[14]['total']['closingbal'];
                // E: Total Pendapatan

                // B: Laba Bersih = EAT (Laba Setelah Pajak)
                $laba_bersih_yoy_month_prev = $data_db[52]['total']['amount_bln_prev_before'];
                $laba_bersih_yoy_month_curr = $data_db[52]['total']['amount_bln_prev'];

                $laba_bersih_yoy_prev = $data_db[52]['total']['amount_bln_before'];
                $laba_bersih_yoy_curr = $data_db[52]['total']['amount_bln'];

                $laba_bersih_ytd_prev = $data_db[52]['total']['closingbal_before'];
                $laba_bersih_ytd_curr = $data_db[52]['total']['closingbal'];
                // E: Laba Bersih = EAT (Laba Setelah Pajak)

                // B: Ebitda
                $ebitda_yoy_prev = $data_db[40]['total']['amount_bln_before'];
                $ebitda_yoy_curr = $data_db[40]['total']['amount_bln'];

                $ebitda_ytd_prev = $data_db[40]['total']['closingbal_before'];
                $ebitda_ytd_curr = $data_db[40]['total']['closingbal'];
                // E: Ebitda

                // B: Net Margin
                $net_profit_margin_yoy_prev = $tot_income_yoy_prev != 0 ? ($laba_bersih_yoy_prev / $tot_income_yoy_prev) * 100 : 0;
                $net_profit_margin_yoy_curr = $tot_income_yoy_curr != 0 ? ($laba_bersih_yoy_curr / $tot_income_yoy_curr) * 100 : 0;

                $net_profit_margin_ytd_prev = $tot_income_ytd_prev != 0 ? ($laba_bersih_ytd_prev / $tot_income_ytd_prev) * 100 : 0;
                $net_profit_margin_ytd_curr = $tot_income_ytd_curr != 0 ? ($laba_bersih_ytd_curr / $tot_income_ytd_curr) * 100 : 0;
                // E: Net Margin

                // B: Beban Langsung
                $beban_yoy_prev = $data_db[26]['total']['amount_bln_before'];
                $beban_yoy_curr = $data_db[26]['total']['amount_bln'];

                $beban_ytd_prev = $data_db[26]['total']['closingbal_before'];
                $beban_ytd_curr = $data_db[26]['total']['closingbal'];
                // E: Beban Langsung
                
                // B: Laba Kotor
                $laba_kotor_yoy_month_prev = $data_db[27]['total']['amount_bln_prev_before'];
                $laba_kotor_yoy_month_curr = $data_db[27]['total']['amount_bln_prev'];

                $laba_kotor_yoy_prev = $data_db[27]['total']['amount_bln_before'];
                $laba_kotor_yoy_curr = $data_db[27]['total']['amount_bln'];

                $laba_kotor_ytd_prev = $data_db[27]['total']['closingbal_before'];
                $laba_kotor_ytd_curr = $data_db[27]['total']['closingbal'];
                // E: Laba Kotor
                
                // B: Biaya Umum
                $biaya_umum_yoy_prev = $data_db[39]['total']['amount_bln_before'];
                $biaya_umum_yoy_curr = $data_db[39]['total']['amount_bln'];

                $biaya_umum_ytd_prev = $data_db[39]['total']['closingbal_before'];
                $biaya_umum_ytd_curr = $data_db[39]['total']['closingbal'];
                // E: Biaya Umum

                // B: Rawat Inap
                $ranap_yoy_prev = $data_db[4]['total']['amount_bln_before'];
                $ranap_yoy_curr = $data_db[4]['total']['amount_bln'];

                $ranap_ytd_prev = $data_db[4]['total']['closingbal_before'];
                $ranap_ytd_curr = $data_db[4]['total']['closingbal'];
                // E: Rawat Inap
                
                // B: Rawat Jalan
                $rajal_yoy_prev = $data_db[5]['total']['amount_bln_before'];
                $rajal_yoy_curr = $data_db[5]['total']['amount_bln'];

                $rajal_ytd_prev = $data_db[5]['total']['closingbal_before'];
                $rajal_ytd_curr = $data_db[5]['total']['closingbal'];
                // E: Rawat Jalan
                
                // B: IGD
                $igd_yoy_prev = $data_db[3]['total']['amount_bln_before'];
                $igd_yoy_curr = $data_db[3]['total']['amount_bln'];

                $igd_ytd_prev = $data_db[3]['total']['closingbal_before'];
                $igd_ytd_curr = $data_db[3]['total']['closingbal'];
                // E: IGD
                
                // B: Penunjang Medis
                $penunjang_yoy_prev = $data_db[6]['total']['amount_bln_before'];
                $penunjang_yoy_curr = $data_db[6]['total']['amount_bln'];

                $penunjang_ytd_prev = $data_db[6]['total']['closingbal_before'];
                $penunjang_ytd_curr = $data_db[6]['total']['closingbal'];
                // E: Penunjang Medis
                
                // B: Lainnya
                $lainnya_yoy_prev = $data_db[7]['total']['amount_bln_before'];
                $lainnya_yoy_curr = $data_db[7]['total']['amount_bln'];

                $lainnya_ytd_prev = $data_db[7]['total']['closingbal_before'];
                $lainnya_ytd_curr = $data_db[7]['total']['closingbal'];
                // E: Lainnya
  
                $rs_pos->MoveNext();
            }
        }

        // B: Data Summary
        $api_data_summary['yoy'] = [
            'tot_income'    => formatKeJT($tot_income_yoy_curr),
            'laba_bersih'   => formatKeJT($laba_bersih_yoy_curr),
            'ebitda'        => formatKeJT($ebitda_yoy_curr),
            'net_margin'    => round($net_profit_margin_yoy_curr, 2).' %',
        ];

        $api_data_summary['ytd'] = [
            'tot_income'    => formatKeJT($tot_income_ytd_curr),
            'laba_bersih'   => formatKeJT($laba_bersih_ytd_curr),
            'ebitda'        => formatKeJT($ebitda_ytd_curr),
            'net_margin'    => round($net_profit_margin_ytd_curr, 2).' %',
        ];
        // E: Data Summary

        // B: Data Different
        $api_data_diff['yoy'] = [
            'pendapatan_prev'   => formatKeJTRound($tot_income_yoy_prev),
            'pendapatan_curr'   => formatKeJTRound($tot_income_yoy_curr),
            'beban_prev'        => formatKeJTRound($beban_yoy_prev),
            'beban_curr'        => formatKeJTRound($beban_yoy_curr),
            'laba_kotor_prev'   => formatKeJTRound($laba_kotor_yoy_prev),
            'laba_kotor_curr'   => formatKeJTRound($laba_kotor_yoy_curr),
            'biaya_umum_prev'   => formatKeJTRound($biaya_umum_yoy_prev),
            'biaya_umum_curr'   => formatKeJTRound($biaya_umum_yoy_curr),
            'ebitda_prev'       => formatKeJTRound($ebitda_yoy_prev),
            'ebitda_curr'       => formatKeJTRound($ebitda_yoy_curr),
            'laba_bersih_prev'  => formatKeJTRound($laba_bersih_yoy_prev),
            'laba_bersih_curr'  => formatKeJTRound($laba_bersih_yoy_curr)
        ];

        $api_data_diff['ytd'] = [
            'pendapatan_prev'   => formatKeJTRound($tot_income_ytd_prev),
            'pendapatan_curr'   => formatKeJTRound($tot_income_ytd_curr),
            'beban_prev'        => formatKeJTRound($beban_ytd_prev),
            'beban_curr'        => formatKeJTRound($beban_ytd_curr),
            'laba_kotor_prev'   => formatKeJTRound($laba_kotor_ytd_prev),
            'laba_kotor_curr'   => formatKeJTRound($laba_kotor_ytd_curr),
            'biaya_umum_prev'   => formatKeJTRound($biaya_umum_ytd_prev),
            'biaya_umum_curr'   => formatKeJTRound($biaya_umum_ytd_curr),
            'ebitda_prev'       => formatKeJTRound($ebitda_ytd_prev),
            'ebitda_curr'       => formatKeJTRound($ebitda_ytd_curr),
            'laba_bersih_prev'  => formatKeJTRound($laba_bersih_ytd_prev),
            'laba_bersih_curr'  => formatKeJTRound($laba_bersih_ytd_curr)
        ];
        // E: Data Different

        // B: Data Komposisi
        $api_data_komposisi['yoy'] = [
            'ranap'         => formatKeJTRound($ranap_yoy_curr),
            'ranap_txt'     => formatKeJT($ranap_yoy_curr),
            'rajal'         => formatKeJTRound($rajal_yoy_curr),
            'rajal_txt'     => formatKeJT($rajal_yoy_curr),
            'igd'           => formatKeJTRound($igd_yoy_curr),
            'igd_txt'       => formatKeJT($igd_yoy_curr),
            'penunjang'     => formatKeJTRound($penunjang_yoy_curr),
            'penunjang_txt' => formatKeJT($penunjang_yoy_curr),
            'lainnya'       => formatKeJTRound($lainnya_yoy_curr),
            'lainnya_txt'   => formatKeJT($lainnya_yoy_curr),
        ];

        $api_data_komposisi['ytd'] = [
            'ranap'         => formatKeJTRound($ranap_ytd_curr),
            'ranap_txt'     => formatKeJT($ranap_ytd_curr),
            'rajal'         => formatKeJTRound($rajal_ytd_curr),
            'rajal_txt'     => formatKeJT($rajal_ytd_curr),
            'igd'           => formatKeJTRound($igd_ytd_curr),
            'igd_txt'       => formatKeJT($igd_ytd_curr),
            'penunjang'     => formatKeJTRound($penunjang_ytd_curr),
            'penunjang_txt' => formatKeJT($penunjang_ytd_curr),
            'lainnya'       => formatKeJTRound($lainnya_ytd_curr),
            'lainnya_txt'   => formatKeJT($lainnya_ytd_curr),
        ];
        // E: Data Komposisi

        // B: Data Tren Margin
        $gross_margin_yoy_prev = $laba_kotor_yoy_month_curr == 0 ? 0 : ($laba_kotor_yoy_month_curr / $tot_income_yoy_month_curr) * 100;
        $gross_margin_yoy_curr = $laba_kotor_yoy_curr == 0 ? 0 : ($laba_kotor_yoy_curr / $tot_income_yoy_curr) * 100;

        $net_margin_yoy_prev = $laba_bersih_yoy_month_curr == 0 ? 0 : ($laba_bersih_yoy_month_curr / $tot_income_yoy_month_curr) * 100;
        $net_margin_yoy_curr = $laba_bersih_yoy_curr == 0 ? 0 : ($laba_bersih_yoy_curr / $tot_income_yoy_curr) * 100;

        $api_data_tren_margin = [
            'gross_margin_prev' => round($gross_margin_yoy_prev, 2),
            'gross_margin_curr' => round($gross_margin_yoy_curr, 2),
            'net_margin_prev'   => round($net_margin_yoy_prev, 2),
            'net_margin_curr'   => round($net_margin_yoy_curr, 2),
        ];
        // E: Data Tren Margin

        // Siapkan palet warna (Bisa disesuaikan dengan tema aplikasi)
        $color_palette = [
            '#1D4ED8', // 1. Royal Blue (Paling dominan/terbesar)
            '#0B663F', // 2. Dark Green
            '#8B4500', // 3. Ochre/Brown
            '#5B21B6', // 4. Indigo/Medium Purple
            '#0A6B66', // 5. Deep Teal/Muted Cyan
            '#C2410C', // 6. Rust Orange
            '#6D28D9', // 7. Vivid Violet/Purple (Potongan besar nomor 2)
            '#0D9488', // 8. Deep Turquoise/Toska Tua (Potongan paling tipis di atas)
            '#E8A820', // 9. Warm Gold (Cadangan pelengkap untuk entitas 9)
            '#8899AA'  // 10. Slate Gray (Cadangan netral untuk entitas 10)
        ];

        $color_idx = 0;

        foreach ($data_cabang as $bc => $cabang)
        {
            $reven_cabang_name[] = $cabang['branch_name'];

            $reven_cabang_color[] = $color_palette[$color_idx % count($color_palette)];
            $color_idx++;

            $reven_cabang_amount_yoy[] = $data_db[14]['branches'][$bc]['amount_bln'] ?? 0;

            $reven_cabang_amount_ytd[] = $data_db[14]['branches'][$bc]['closingbal'] ?? 0;

            $reven_cabang_detail_yoy[] = [
                'igd'               => $data_db[3]['branches'][$bc]['amount_bln'] ?? 0,
                'ranap'             => $data_db[4]['branches'][$bc]['amount_bln'] ?? 0,
                'rajal'             => $data_db[5]['branches'][$bc]['amount_bln'] ?? 0,
                'penunjang'         => $data_db[6]['branches'][$bc]['amount_bln'] ?? 0,
                'lainnya'           => $data_db[7]['branches'][$bc]['amount_bln'] ?? 0,
                'bruto'             => $data_db[8]['branches'][$bc]['amount_bln'] ?? 0,
                'pengurangan'       => $data_db[9]['branches'][$bc]['amount_bln'] ?? 0,
                'non_operasional'   => $data_db[13]['branches'][$bc]['amount_bln'] ?? 0,
                'bersih'            => $data_db[14]['branches'][$bc]['amount_bln'] ?? 0,
                'langsung'          => $data_db[26]['branches'][$bc]['amount_bln'] ?? 0,
                'kotor'             => $data_db[27]['branches'][$bc]['amount_bln'] ?? 0,
                'opex'              => $data_db[39]['branches'][$bc]['amount_bln'] ?? 0,
                'ebitda'            => $data_db[40]['branches'][$bc]['amount_bln'] ?? 0,
                'ebit'              => $data_db[47]['branches'][$bc]['amount_bln'] ?? 0,
                'eat'               => $data_db[52]['branches'][$bc]['amount_bln'] ?? 0,
            ];

            $reven_cabang_detail_ytd[] = [
                'igd'               => $data_db[3]['branches'][$bc]['closingbal'] ?? 0,
                'ranap'             => $data_db[4]['branches'][$bc]['closingbal'] ?? 0,
                'rajal'             => $data_db[5]['branches'][$bc]['closingbal'] ?? 0,
                'penunjang'         => $data_db[6]['branches'][$bc]['closingbal'] ?? 0,
                'lainnya'           => $data_db[7]['branches'][$bc]['closingbal'] ?? 0,
                'bruto'             => $data_db[8]['branches'][$bc]['closingbal'] ?? 0,
                'pengurangan'       => $data_db[9]['branches'][$bc]['closingbal'] ?? 0,
                'non_operasional'   => $data_db[13]['branches'][$bc]['closingbal'] ?? 0,
                'bersih'            => $data_db[14]['branches'][$bc]['closingbal'] ?? 0,
                'langsung'          => $data_db[26]['branches'][$bc]['closingbal'] ?? 0,
                'kotor'             => $data_db[27]['branches'][$bc]['closingbal'] ?? 0,
                'opex'              => $data_db[39]['branches'][$bc]['closingbal'] ?? 0,
                'ebitda'            => $data_db[40]['branches'][$bc]['closingbal'] ?? 0,
                'ebit'              => $data_db[47]['branches'][$bc]['closingbal'] ?? 0,
                'eat'               => $data_db[52]['branches'][$bc]['closingbal'] ?? 0,
            ];
        }

        // B: Revenue Bersih Per Cabang
        $api_data_revenue_cabang['yoy'] = [
            'cabang'    => $reven_cabang_name,
            'amount'    => $reven_cabang_amount_yoy,
            'warna'     => $reven_cabang_color,
            'detail'    => $reven_cabang_detail_yoy,
        ];

        $api_data_revenue_cabang['ytd'] = [
            'cabang'    => $reven_cabang_name,
            'amount'    => $reven_cabang_amount_ytd,
            'warna'     => $reven_cabang_color,
            'detail'    => $reven_cabang_detail_ytd,
            
        ];
        // E: Revenue Bersih Per Cabang

        $dataJSON = array(
            'bulan_prev'                    => monthnamelong($data['prev_month']),
            'bulan_curr'                    => monthnamelong($data['month']),
            'year_prev'                     => $data2['year_prev'],
            'year_curr'                     => $data['year'],
            'data_summary'                  => $api_data_summary,
            'data_diff'                     => $api_data_diff,
            'data_komposisi'                => $api_data_komposisi,
            'data_tren_margin'              => $api_data_tren_margin,
            'data_revenue_cabang'           => $api_data_revenue_cabang,
        );

        $this->response($dataJSON, REST::HTTP_OK);
    } /*}}}*/
}
?>