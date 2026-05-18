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
                // $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                if ($data['bid'] == -1 && $rs->fields['kdbid'] == 2) $bc = self::$ho_jkk;
                elseif ($data['bid'] == -1 && $rs->fields['kdbid'] == 3) $bc = self::$ho_kah;
                else $bc = $rs->fields['branch_code'];

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
                // $bc = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];
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

            $total_aktiva       = 0;
            $total_ekuitas      = 0;
            $total_kewajiban    = 0;
            $total_rl_tahun_berjalan          = 0;
            $total_ht_jangka_panjang          = 0;
            $dash_total_asset_lancar          = 0;
            $dash_total_asset_tidak_lancar    = 0;

            while (!$rs_pos->EOF)
            {
                $pnid = $rs_pos->fields['pnid'];
                $row = $data_db[$pnid] ?? [];

                $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';

                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $classna = '';

                if (trim($nama) == '') $nama = '&nbsp;';
                if ($is_header == 't') $namanama = '<b>'.$nama.'</b>';
                if ($is_header == 't') $classna = 'sg';

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

                $amt_tot_prev       = format_uang($tot_op, 2);
                $amt_tot            = format_uang($tot_cl, 2);
                $amt_tot_noformat   = $tot_cl;

                if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_tot_prev       = '';
                    $amt_tot            = '';
                    $amt_tot_noformat   = '';
                }

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $amt_tot_prev       = '<b><u>'.$amt_tot_prev.'</u></b>';
                    $amt_tot            = '<b><u>'.$amt_tot.'</u></b>';
                    $amt_tot_noformat   = $amt_tot_noformat;
                }

                $tmp_amounts['total'] = [
                    'amount_prev'   => $amt_tot_prev,
                    'amount'        => $amt_tot,
                    'amount_aktiva' => $amt_tot_noformat,
                ];


                $tmpdata = array();
                $tmpdata['classna'] = $classna;
                $tmpdata['nama_pos'] = $space.$nama;
                $tmpdata['amounts']  = $tmp_amounts;


                $data_pos[$rs_pos->fields['jenis_pos']][$pnid] = $tmpdata;

                if ($rs_pos->fields['jenis_pos'] == 1) $empty_aktiva = false;
                elseif ($rs_pos->fields['jenis_pos'] == 2) $empty_pasiva = false;



            if (in_array($rs_pos->fields['pnid'], [23, 24, 25, 26, 27])) {

                $total_ekuitas  += $amt_tot_noformat;
            }

            if (in_array($rs_pos->fields['pnid'], [16, 17, 18, 19, 20, 21, 22 ])) {

                $total_kewajiban  += $amt_tot_noformat;
            }

            if (in_array($rs_pos->fields['pnid'], [26])) {

                $total_rl_tahun_berjalan  += $amt_tot_noformat;
            }


            if (in_array($rs_pos->fields['pnid'], [22])) {

                $total_ht_jangka_panjang  += $amt_tot_noformat;
            }

            if (in_array($rs_pos->fields['pnid'], [2,3,4,5,6,7,8,9,10])) {

                $dash_total_asset_lancar  += $amt_tot_noformat;
            }


            if (in_array($rs_pos->fields['pnid'], [11,12,13])) {

                $dash_total_asset_tidak_lancar  += $amt_tot_noformat;
            }

            if (in_array($rs_pos->fields['pnid'], [16,17,18,19,20,21])) {

                $dash_total_kewajiban_jk_pendek  += $amt_tot_noformat;
            }


/*
echo $rs_pos->fields['pnid'].' == '.$nama;
echo '<br>';
*/

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



        // 1. Ambil data dari index [1] (Aktiva)
        $data_aktiva = $data_pos[1];

        // 2. Bersihkan data (buang baris judul/total & pastikan nilainya angka)
        $filtered = array_filter($data_aktiva, function($row) {
            return !empty($row['amounts']['total']['amount_aktiva']) && 
                   !str_contains($row['nama_pos'], 'TOTAL'); // Abaikan baris "TOTAL AKTIVA"
        });

        // 3. Urutkan dari besar ke kecil (Descending)
        usort($filtered, function($a, $b) {
            return $b['amounts']['total']['amount_aktiva'] <=> $a['amounts']['total']['amount_aktiva'];
        });

        // 4. Potong ambil 3 saja
        $bar_aktiva = array_slice($filtered, 0, 12);



        $total_aktiva                   = formatKeJT($tmp_amounts['total']['amount_aktiva']);
        $total_aktiva_sub               = $tmp_amounts['total']['amount'];

        $total_ekuitas_persen           = round(($total_ekuitas / $tmp_amounts['total']['amount_aktiva'] )* 100 );
        $total_ekuitas_no_format        = $total_ekuitas;
        $total_ekuitas                  = formatKeJT($total_ekuitas);

        $total_kewajiban_noformat       = $total_kewajiban;
        $total_kewajiban                = formatKeJT($total_kewajiban);

        $total_rl_tahun_berjalan_noformat        = $total_rl_tahun_berjalan;
        $total_rl_tahun_berjalan_sub             = format_uang($total_rl_tahun_berjalan, 2);
        $total_rl_tahun_berjalan                 = formatKeJT($total_rl_tahun_berjalan);


        $dash_total_asset_lancar_noformat       = $dash_total_asset_lancar;
        $dash_total_asset_lancar_persen         = round(($dash_total_asset_lancar / $tmp_amounts['total']['amount_aktiva'] )* 100 );
        $dash_total_asset_lancar                = formatKeJT($dash_total_asset_lancar);

        $dash_total_asset_tidak_lancar_persen         = round(($dash_total_asset_tidak_lancar / $tmp_amounts['total']['amount_aktiva'] )* 100 );
        $dash_total_asset_tidak_lancar                = formatKeJT($dash_total_asset_tidak_lancar);

        $total_ht_jangka_panjang_persen                = round(($total_ht_jangka_panjang / $tmp_amounts['total']['amount_aktiva'] )* 100 );
        $total_ht_jangka_panjang                       = formatKeJT($total_ht_jangka_panjang);

        $dash_total_kewajiban_jk_pendek_noformat       = $dash_total_kewajiban_jk_pendek;
        $dash_total_kewajiban_jk_pendek_persen         = round(($dash_total_kewajiban_jk_pendek / $tmp_amounts['total']['amount_aktiva'] )* 100 );
        $dash_total_kewajiban_jk_pendek                = formatKeJT($dash_total_kewajiban_jk_pendek);


        $curr_ratio                     =  round($dash_total_asset_lancar_noformat / $dash_total_kewajiban_jk_pendek_noformat,2).'x';
        $dta                            =  round(($total_kewajiban_noformat / $tmp_amounts['total']['amount_aktiva']) * 100,2);
        $dte                            =  round(($total_kewajiban_noformat / $total_ekuitas_no_format),2);
        $er                             =  round(($total_ekuitas_no_format / $tmp_amounts['total']['amount_aktiva']) * 100,2);
        $roa                            =  round(($total_rl_tahun_berjalan_noformat / $tmp_amounts['total']['amount_aktiva']) * 100,2);
        $wc                             =  formatKeJT($dash_total_asset_lancar_noformat - $dash_total_kewajiban_jk_pendek_noformat);



        // 1. Ambil data dari index [2] (Pasiva)
        $data_pasiva = $data_pos[2];

        // 2. Bersihkan data (buang baris judul/total & pastikan nilainya angka)
        $pasiva_filtered = array_filter($data_pasiva, function($row) {
            return !empty($row['amounts']['total']['amount_aktiva']) && 
                   !str_contains($row['nama_pos'], 'TOTAL'); // Abaikan baris "TOTAL AKTIVA"
        });

        // 3. Urutkan dari besar ke kecil (Descending)
        usort($pasiva_filtered, function($a, $b) {
            return $b['amounts']['total']['amount_aktiva'] <=> $a['amounts']['total']['amount_aktiva'];
        });

        // 4. Potong ambil 3 saja
        $dash_pasiva_top_3 = array_slice($pasiva_filtered, 0, 8);
        $dash_pasiva_lainnya = array_slice($pasiva_filtered, 8);


        $hide_jkk      = ($data['bid'] == '-3')  ? 'display:none' : '';
        $tahuna        = get_combo_option_year($tahun, 2024, $tahun+1);


        return view('dashboard.overview', compact(
            'tgl_cetak',
            'data',
            'bln_prev',
            'bln',
            'rs_pos',
            'cmb_cabang',
            'bulan',
            'tahun',
            'bulana',
            'bulan_nama',
            'tahuna',

            'bar_aktiva',

            'total_aktiva',
            'total_kewajiban',
            'dash_total_asset_lancar',
            'total_ekuitas',

            'data_cabang',
            'data_pos',
        ));
    } /*}}}*/



}
?>
