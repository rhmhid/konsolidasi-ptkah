<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Overview extends BaseController
{
    static $ho_jkk, $ho_kah;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaMdl');
        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');

        self::$ho_kah = dataConfigs('default_kode_branch_kah');

    } /*}}}*/

        public function list () /*{{{*/
            {
                $data = array(
                    'bid'           => get_var('bid',-1),
                    'month'         => intval(get_var('month') ?: date('n')),
                    'year'          => get_var('year') ?: date('Y'),
                );

                $data_cabang = Modules::data_cabang_executive();
                $cmb_cabang = $data_cabang->GetMenu2('', get_var('bid'), false, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

                $bulan      = $data['month'];
                $bulan_nama = date('M', mktime(0, 0, 0, $bulan, 10));

                $tahun      = $data['year'];
                $bulana     = get_combo_option_month_lk( $bulan );

                $rs_cabang = Modules::data_cabang_all('', $data['bid'], 'f');

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

                $neracaData = $this->getNeracaData($data,$data_cabang); 
                    
                    $data_pos   = $neracaData['data_pos'];
                    $bar_aktiva = $neracaData['bar_aktiva'];
                    $bar_pasiva = $neracaData['bar_pasiva'];
                    $raw        = $neracaData['raw_values']; 

                    $total_aktiva_raw = $raw['total_aktiva'] ?: 0;

                    $total_ekuitas_persen           = $total_aktiva_raw ? round(($raw['total_ekuitas'] / $total_aktiva_raw) * 100) : 0;
                    $dash_total_asset_lancar_persen = $total_aktiva_raw ? round(($raw['dash_total_asset_lancar'] / $total_aktiva_raw) * 100) : 0;
                    $dash_total_asset_tidak_lancar_persen = $total_aktiva_raw ? round(($raw['dash_total_asset_tidak_lancar'] / $total_aktiva_raw) * 100) : 0;
                    $total_ht_jangka_panjang_persen = $total_aktiva_raw ? round(($raw['total_ht_jangka_panjang'] / $total_aktiva_raw) * 100) : 0;
                    $dash_total_kewajiban_jk_pendek_persen = $total_aktiva_raw ? round(($raw['dash_total_kewajiban_jk_pendek'] / $total_aktiva_raw) * 100) : 0;

                    $curr_ratio = $raw['dash_total_kewajiban_jk_pendek'] ? round($raw['dash_total_asset_lancar'] / $raw['dash_total_kewajiban_jk_pendek'], 2).'x' : '0x';


                    $dta        = $total_aktiva_raw ? round(($raw['total_kewajiban'] / $total_aktiva_raw) * 100, 2) : 0;
                    $dte        = $raw['total_ekuitas'] ? round(($raw['total_kewajiban'] / $raw['total_ekuitas']), 2) : 0;
                    $er         = $total_aktiva_raw ? round(($raw['total_ekuitas'] / $total_aktiva_raw) * 100, 2) : 0;
                    $roa        = $total_aktiva_raw ? round(($raw['total_rl_tahun_berjalan'] / $total_aktiva_raw) * 100, 2) : 0;
                    $wc         = formatKeJT($raw['dash_total_asset_lancar'] - $raw['dash_total_kewajiban_jk_pendek']);

                    // 3. FORMATTING STRING UNTUK VIEW (Dilakukan paling akhir)
                    $total_aktiva                   = formatKeJT($raw['total_aktiva']);
                    $total_ekuitas                  = formatKeJT($raw['total_ekuitas']);
                    $total_kewajiban                = formatKeJT($raw['total_kewajiban']);
                    $total_rl_tahun_berjalan        = formatKeJT($raw['total_rl_tahun_berjalan']);
                    $dash_total_asset_lancar        = formatKeJT($raw['dash_total_asset_lancar']);
                    $dash_total_asset_tidak_lancar  = formatKeJT($raw['dash_total_asset_tidak_lancar']);
                    $total_ht_jangka_panjang        = formatKeJT($raw['total_ht_jangka_panjang']);
                    $dash_total_kewajiban_jk_pendek = formatKeJT($raw['dash_total_kewajiban_jk_pendek']);

                    $hide_jkk   = ($data['bid'] == '-3') ? 'display:none' : '';
                    $tahuna     = get_combo_option_year($tahun, 2024, $tahun+1);

                    $data_aktiva = $data_pos[1] ?? [];
                    $filtered = array_values(array_filter($data_aktiva, function($row) {
                        // Ambil nilainya, pastikan tidak null atau string kosong, tapi angka 0 tetap boleh lolos
                        $amount = $row['amounts']['total']['amount_aktiva'] ?? null;
                        
                        return $amount !== null && $amount !== '' && 
                               !str_contains(strtoupper($row['nama_pos']), 'TOTAL');
                    }));
                    $bar_aktiva = array_slice($filtered, 0, 12);
                    $data_pasiva = $data_pos[2] ?? [];

                    $pasiva_filtered = array_values(array_filter($data_pasiva, function($row) {
                        $amount = $row['amounts']['total']['amount_aktiva'] ?? null;
                        
                        $nama_pos_upper = strtoupper($row['nama_pos']);
                        return $amount !== null && $amount !== '' && 
                               !str_contains($nama_pos_upper, 'TOTAL') &&
                               !str_contains($nama_pos_upper, 'PASIVA');
                    }));

                    $bar_pasiva = array_slice($pasiva_filtered, 0, 12);

                    return view('dashboard.overview', compact(
                                            // Data Umum & Filter
                                            'tgl_cetak', 
                                            'data', 
                                            'cmb_cabang', 
                                            'bulan', 
                                            'tahun', 
                                            'bulana', 
                                            'bulan_nama', 
                                            'tahuna',

                                            // Data Struktur Pasiva/Aktiva & Chart
                                            'bar_aktiva', 
                                            'bar_pasiva', 
                                            'data_pos',

                                            // Nominal Finansial (Format JT)
                                            'total_aktiva', 
                                            'total_kewajiban', 
                                            'dash_total_asset_lancar', 
                                            'total_ekuitas',
                                            'dash_total_asset_tidak_lancar', 
                                            'total_ht_jangka_panjang', 
                                            'dash_total_kewajiban_jk_pendek',

                                            // Rasio Analisis Keuangan
                                            'curr_ratio', 
                                            'dta', 
                                            'dte', 
                                            'er', 
                                            'roa', 
                                            'wc',

                                            // Persentase Dashboard (%)
                                            'total_ekuitas_persen', 
                                            'dash_total_asset_lancar_persen', 
                                            'dash_total_asset_tidak_lancar_persen', 
                                            'total_ht_jangka_panjang_persen', 
                                            'dash_total_kewajiban_jk_pendek_persen'
                                        ));
            } /*}}}*/


        private function getNeracaData($data, $data_cabang)
        {

            $data_db = [];
            $data_pos = [];
            $empty_aktiva = $empty_pasiva = true;

            $total_ekuitas = 0;
            $total_kewajiban = 0;
            $total_rl_tahun_berjalan = 0;
            $total_ht_jangka_panjang = 0;
            $dash_total_asset_lancar = 0;
            $dash_total_asset_tidak_lancar = 0;
            $dash_total_kewajiban_jk_pendek = 0;

            $rs_period = Modules::get_period_akunting($data);

            if (!$rs_period->EOF) {
                $data['paid']   = $rs_period->fields['paid'];
                $data['pbegin'] = $rs_period->fields['pbegin'];
                $data['pend']   = $rs_period->fields['pend'];

                // 1. Proses Data Neraca
                $rs = NeracaMdl::list($data);
                while (!$rs->EOF) {
                    if ($data['bid'] == -1 && $rs->fields['kdbid'] == 2) $bc = self::$ho_jkk;
                    elseif ($data['bid'] == -1 && $rs->fields['kdbid'] == 3) $bc = self::$ho_kah;
                    else $bc = $rs->fields['branch_code'];

                    if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f') {
                        $openingbal = $rs->fields['openingbal'] * -1;
                        $closingbal = $rs->fields['closingbal'] * -1;
                    } else {
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
                while (!$rss->EOF) {
                    if ($data['bid'] == -1 && $rss->fields['kdbid'] == 2) $bc = self::$ho_jkk;
                    elseif ($data['bid'] == -1 && $rss->fields['kdbid'] == 3) $bc = self::$ho_kah;
                    else $bc = $rss->fields['branch_code'];

                    $pnid = $rss->fields['pnid'];
                    $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $rss->fields['openingbal'];
                    $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $rss->fields['closingbal'];
                    $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $rss->fields['openingbal'];
                    $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $rss->fields['closingbal'];

                    $rss->MoveNext();
                }

                $rs_pos = NeracaMdl::list_pos();
                $tmp_amounts = [];

                while (!$rs_pos->EOF) {
                    $pnid = $rs_pos->fields['pnid'];
                    $row = $data_db[$pnid] ?? [];

                    $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                    $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                    $nama = $rs_pos->fields['kode_pos'] . ' ' . $rs_pos->fields['nama_pos'];
                    $classna = '';

                    if (trim($nama) == '') $nama = '&nbsp;';
                    if ($is_header == 't') $classna = 'sg';

                    $tot_op = $row['total']['openingbal'] ?? 0;
                    $tot_cl = $row['total']['closingbal'] ?? 0;

                    foreach ($data_cabang as $bc => $cabang) {
                        $op = $row['branches'][$bc]['openingbal'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $amt_prev = format_uang($op, 2);
                        $amt = format_uang($cl, 2);

                        if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f') {
                            $amt_prev = $amt = '';
                        }
                        if ($rs_pos->fields['sum_total'] == 't') {
                            $amt_prev = '<b><u>' . $amt_prev . '</u></b>';
                            $amt = '<b><u>' . $amt . '</u></b>';
                        }

                        $tmp_amounts['branches'][$bc] = ['amount_prev' => $amt_prev, 'amount' => $amt];
                    }

                    $amt_tot_prev = format_uang($tot_op, 2);
                    $amt_tot = format_uang($tot_cl, 2);
                    $amt_tot_noformat = $tot_cl;

                    if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f') {
                        $amt_tot_prev = $amt_tot = $amt_tot_noformat = '';
                    }
                    if ($rs_pos->fields['sum_total'] == 't') {
                        $amt_tot_prev = '<b><u>' . $amt_tot_prev . '</u></b>';
                        $amt_tot = '<b><u>' . $amt_tot . '</u></b>';
                    }

                    $tmp_amounts['total'] = [
                        'amount_prev'   => $amt_tot_prev,
                        'amount'        => $amt_tot,
                        'amount_aktiva' => $amt_tot_noformat,
                    ];

                    $tmpdata = [
                        'classna'  => $classna,
                        'nama_pos' => $space . $nama,
                        'amounts'  => $tmp_amounts
                    ];

                    $data_pos[$rs_pos->fields['jenis_pos']][$pnid] = $tmpdata;

                    // Hitung total-total komponen untuk rasio keuangan
                    if (in_array($pnid, [23, 24, 25, 26, 27]))      $total_ekuitas += $amt_tot_noformat;
                    if (in_array($pnid, [16, 17, 18, 19, 20, 21, 22])) $total_kewajiban += $amt_tot_noformat;
                    if (in_array($pnid, [26]))                      $total_rl_tahun_berjalan += $amt_tot_noformat;
                    if (in_array($pnid, [22]))                      $total_ht_jangka_panjang += $amt_tot_noformat;
                    if (in_array($pnid, [2, 3, 4, 5, 6, 7, 8, 9, 10])) $dash_total_asset_lancar += $amt_tot_noformat;
                    if (in_array($pnid, [11, 12, 13]))              $dash_total_asset_tidak_lancar += $amt_tot_noformat;
                    if (in_array($pnid, [16, 17, 18, 19, 20, 21]))   $dash_total_kewajiban_jk_pendek += $amt_tot_noformat;



                    // Akumulasi nilai ke Parent Pos
                    if ($rs_pos->fields['parent_pnid'] != '') {
                        $parent_id = $rs_pos->fields['parent_pnid'];
                        foreach ($data_cabang as $bc => $cabang) {
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

            $total_aktiva_raw = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? 0;

            return [
                'data_pos'          => $data_pos,
                'bar_aktiva'        => $bar_aktiva, // Data untuk chart aktiva
                'bar_pasiva'        => $bar_pasiva, // Data untuk chart pasiva
                'raw_values'        => [
                    'total_aktiva'                  => $total_aktiva_raw,
                    'total_ekuitas'                 => $total_ekuitas,
                    'total_kewajiban'               => $total_kewajiban,
                    'total_rl_tahun_berjalan'       => $total_rl_tahun_berjalan,
                    'dash_total_asset_lancar'       => $dash_total_asset_lancar,
                    'dash_total_asset_tidak_lancar' => $dash_total_asset_tidak_lancar,
                    'total_ht_jangka_panjang'       => $total_ht_jangka_panjang,
                    'dash_total_kewajiban_jk_pendek'=> $dash_total_kewajiban_jk_pendek,
                ]
            ];
        }


}
?>
