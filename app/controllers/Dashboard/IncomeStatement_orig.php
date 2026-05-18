<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class IncomeStatement extends BaseController
{
    static $ho_jkk, $ho_kah;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model(array('AkuntansiReport/LabaRugiMdl', 'Dashboard/DashboardMdl'));

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');

        self::$ho_kah = dataConfigs('default_kode_branch_kah');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', get_var('bid'), true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        $data = array(
            'bid'   => get_var('bid'),
            'month' => get_var('month', date('n')),
            'year'  => get_var('year', date('Y')),
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

        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs_now = LabaRugiMdl::list($data);

            $data_now = $rs_now->GetArray();

            $data2 = $data;

            $data2['year'] = $data['year'] -1;

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
                    $amount_bln = $rs->fields['amount_bln'] * -1;
                    $amount_bln_before = $rs->fields['amount_bln_before'] * -1;
                }
                else
                {
                    $amount_bln = $rs->fields['amount_bln'];
                    $amount_bln_before = $rs->fields['amount_bln_before'];
                }

                $data_db[$pplrid]['branches'][$bc]['amount_bln'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln'] ?? 0) + $amount_bln;
                $data_db[$pplrid]['branches'][$bc]['amount_bln_before'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln_before'] ?? 0) + $amount_bln_before;

                $data_db[$pplrid]['total']['amount_bln'] = ($data_db[$pplrid]['total']['amount_bln'] ?? 0) + $amount_bln;
                $data_db[$pplrid]['total']['amount_bln_before'] = ($data_db[$pplrid]['total']['amount_bln_before'] ?? 0) + $amount_bln_before;

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
                $tot_bln         = $row['total']['amount_bln'] ?? 0;
                $tot_bln_before  = $row['total']['amount_bln_before'] ?? 0;
                foreach ($data_cabang as $bc => $cabang)
                {
                    $amt_bln  = format_uang($row['branches'][$bc]['amount_bln'] ?? 0, 2);
                    $amt_bln_before  = format_uang($row['branches'][$bc]['amount_bln_before'] ?? 0, 2);

                    if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_bln = '';
                        $amt_bln_before = '';
                    }

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $amt_bln = '<b><u>'.$amt_bln.'</u></b>'; 
                        $amt_bln_before = '<b><u>'.$amt_bln_before.'</u></b>';
                    }

                    $tmp_amounts['branches'][$bc] = [
                        'amount_bln'        => $amt_bln, 
                        'amount_bln_before' => $amt_bln_before
                    ];
                }

                $amt_tot_bln  = format_uang($tot_bln, 2);
                $amt_tot_bln_before  = format_uang($tot_bln_before, 2);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_tot_bln = '';
                    $amt_tot_bln_before = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_tot_bln = '<b><u>'.$amt_tot_bln.'</u></b>'; 
                    $amt_tot_bln_before = '<b><u>'.$amt_tot_bln_before.'</u></b>';
                }

                $tmp_amounts['total'] = [
                    'amount_bln'               => $amt_tot_bln, 
                    'amount_bln_before'        => $amt_tot_bln_before
                ];

                $tmpdata = array();
                $tmpdata['nama_pos'] = $space.$nama;
                $tmpdata['amounts']  = $tmp_amounts;

                $data_pos[$pplrid] = $tmpdata;
                $empty_pos = false;

                if ($rs_pos->fields['parent_pplrid'] != '')
                {
                    $parent_pplrid = $rs_pos->fields['parent_pplrid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $b = $row['branches'][$bc]['amount_bln'] ?? 0;
                        $bf = $row['branches'][$bc]['amount_bln_before'] ?? 0;

                        $data_db[$parent_pplrid]['branches'][$bc]['amount_bln'] = ($data_db[$parent_pplrid]['branches'][$bc]['amount_bln'] ?? 0) + $b;
                        $data_db[$parent_pplrid]['branches'][$bc]['amount_bln_before'] = ($data_db[$parent_pplrid]['branches'][$bc]['amount_bln_before'] ?? 0) + $bf;

                        // echo "<pre>";
                        // echo "<br />pplrid : ".$pplrid;
                        // echo "<br />parent_pplrid : ".$parent_pplrid;
                        // echo "<br />b : ".$b;
                        // echo "<br />bf : ".$bf;
                        // echo "<br />data_db : ".$data_db[52]['branches'][$bc]['amount_bln'];
                        // echo "<br />data_db : ".$data_db[52]['branches'][$bc]['amount_bln_before'];
                        // echo "</pre>";

                        if ($pplrid == 3)
                        {
                            $tot_pend_igd         += $b;
                            $tot_pend_igd_before  += $bf;
                        }
                        elseif ($pplrid == 4)
                        {
                            $tot_pend_ranap         += $b;
                            $tot_pend_ranap_before  += $bf;
                        }
                        elseif ($pplrid == 5)
                        {
                            $tot_pend_rajal         += $b;
                            $tot_pend_rajal_before  += $bf;
                        }
                        elseif ($pplrid == 6)
                        {
                            $tot_pend_penunjang         += $b;
                            $tot_pend_penunjang_before  += $bf;
                        }
                        elseif ($pplrid == 7)
                        {
                            $tot_pend_lainya         += $b;
                            $tot_pend_lainya_before  += $bf;
                        }
                        elseif ($pplrid == 14)
                        {
                            // TOTAL PENDAPATAN
                            $tot_pendapatan         += $b;
                            $tot_pendapatan_before  += $bf;
                        }
                        elseif ($pplrid == 40)
                        {
                            // TOTAL EBITDA
                            $tot_ebitda             += ($b * 1);
                            $tot_ebitda_before      += ($bf * 1);
                        }
                        elseif ($pplrid == 47)
                        {
                            // TOTAL EBIT
                            $tot_ebit               += ($b * 1);
                            $tot_ebit_before        += ($bf * 1);
                        }
                        elseif ($pplrid == 50)
                        {
                            // TOTAL EAT
                            $tot_eat               += ($b * 1);
                            $tot_eat_before        += ($bf * 1);
                        }
                        elseif ($pplrid == 26)
                        {
                            $tot_beban               += ($b * 1);
                            $tot_beban_before        += ($bf * 1);
                        }
                        elseif ($pplrid == 27)
                        {
                            $tot_laba_kotor         += ($b * 1);
                            $tot_laba_kotor_before  += ($bf * 1);
                        }
                        elseif ($pplrid == 39)
                        {
                            $tot_opex               += ($b * 1);
                            $tot_opex_before        += ($bf * 1);
                        }
                    }
                }
  
                $rs_pos->MoveNext();
            }
        }
        // die;

        $bulan      = $data['month'];
        $bulan_nama = date('M', mktime(0, 0, 0, $bulan, 10));

        $tahun      = $data['year'];
        $tahunlalu  = $data2['year'];

        $bulana     = get_combo_option_month_lk($bulan);

        $tot_pend_igd                           = formatKeJTRound($tot_pend_igd);
        $tot_pend_ranap                         = formatKeJTRound($tot_pend_ranap);
        $tot_pend_rajal                         = formatKeJTRound($tot_pend_rajal);
        $tot_pend_lainya                        = formatKeJTRound($tot_pend_lainya);
        $tot_pend_penunjang                     = formatKeJTRound($tot_pend_penunjang);

        $tot_pendapatan_round                   = formatKeJTRound($tot_pendapatan);
        $tot_pendapatan_format                  = formatKeJT($tot_pendapatan);
        $tot_pendapatan_before_round            = formatKeJTRound($tot_pendapatan_before);
        $tot_pendapatan_before_format           = formatKeJT($tot_pendapatan_before);

        $tot_ebitda_round                       = formatKeJTRound($tot_ebitda);
        $tot_ebitda_format                      = formatKeJT($tot_ebitda);
        $tot_ebitda_before_round                = formatKeJTRound($tot_ebitda_before);
        $tot_ebitda_before_format               = formatKeJT($tot_ebitda_before);

        $tot_eat_round                          = formatKeJTRound($tot_eat);
        $tot_eat_format                         = formatKeJT($tot_eat);
        $tot_eat_before_round                   = formatKeJTRound($tot_eat_before);
        $tot_eat_before_format                  = formatKeJT($tot_eat_before);

        $tot_laba_kotor_round                   = formatKeJTRound($tot_laba_kotor);
        $tot_laba_kotor_format                  = formatKeJT($tot_laba_kotor);
        $tot_laba_kotor_before_round            = formatKeJTRound($tot_laba_kotor_before);
        $tot_laba_kotor_before_format           = formatKeJT($tot_laba_kotor_before);

        $tot_beban_round                        = formatKeJTRound($tot_beban);    
        $tot_beban_format                       = formatKeJT($tot_beban);
        $tot_beban_before_round                 = formatKeJTRound($tot_beban_before);
        $tot_beban_before_format                = formatKeJT($tot_beban_before);

        $tot_opex_round                         = formatKeJTRound($tot_opex);
        $tot_opex_format                        = formatKeJT($tot_opex);
        $tot_opex_before_round                  = formatKeJTRound($tot_opex_before);
        $tot_opex_before_format                 = formatKeJT($tot_opex_before);

        $net_margin = round(($tot_pendapatan != 0) ? ($tot_eat / $tot_pendapatan) * 100 : 0, 2);
    
        $tahuna = get_combo_option_year($tahun, 2024, $tahun+1);

        return view('dashboard.income_statement', compact(
            'cmb_cabang',

            'tot_pend_igd',
            'tot_pend_ranap',
            'tot_pend_rajal',
            'tot_pend_lainya',
            'tot_pend_penunjang',

            'tot_pendapatan_round',
            'tot_pendapatan_format',
            'tot_pendapatan_before_round',
            'tot_pendapatan_before_format',

            'tot_ebitda_round',
            'tot_ebitda_format',
            'tot_ebitda_before_round',
            'tot_ebitda_before_format',

            'tot_eat_round',
            'tot_eat_format',
            'tot_eat_before_round',
            'tot_eat_before_format',

            'tot_laba_kotor_round',
            'tot_laba_kotor_format',
            'tot_laba_kotor_before_round',
            'tot_laba_kotor_before_format',

            'tot_beban_round',
            'tot_beban_format',
            'tot_beban_before_round',
            'tot_beban_before_format',

            'tot_opex_round',
            'tot_opex_format',
            'tot_opex_before_round',

            'net_margin',
            'bulan',
            'bulana',
            'bulan_nama',
            'tahun',
            'tahuna',
            'tahunlalu',
        ));
    } /*}}}*/
}
?>