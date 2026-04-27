<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class IncomeStatement extends BaseController
{
    static $ho_jkk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/LabaRugiMdl');
        $this->load->model('Dashboard/DashboardMdl');

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', get_var('bid'), true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month') ?: date('n')),
            'year'  => get_var('year') ?: date('Y'),
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

            $rs_now     = LabaRugiMdl::list($data);


            $data2 = $data;

            $rs_now           = LabaRugiMdl::list($data);

            $data2['year']    = get_var('year') -1 ?: (date('Y') - 1);

            $rs_before         = LabaRugiMdl::list($data2);


            $data_now    = $rs_now->GetArray();
            $data_before = $rs_before->GetArray();


            $rs               = DashboardMdl::list($data_now,$data_before,$data['year']);
            die('sinih');


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

                        $b = $row['branches'][$bc]['amount_bln'] ?? 0;
                        $data_db[$parent_id]['branches'][$bc]['amount_bln'] = ($data_db[$parent_id]['branches'][$bc]['amount_bln'] ?? 0) + $b;

                          if ($rs_pos->fields['pplrid'] == 10) {
                                // TOTAL PENDAPATAN
                                $tot_pendapatan += $b;
                          }
                          if ($rs_pos->fields['pplrid'] == 27) {
                                // TOTAL PENGURANG PENDAPATAN
                                $tot_laba_kotor += ($b * 1);
                          }
                          if ($rs_pos->fields['pplrid'] == 40) {
                                // TOTAL EBITDA
                                $tot_ebitda += ($b * 1);
                          }
                          if ($rs_pos->fields['pplrid'] == 47) {
                                // TOTAL EBIT
                                $tot_ebit += ($b * 1);
                          }


                    }
  
                }
  
                $rs_pos->MoveNext();
            }
      }
  
                  echo $tot_pendapatan;
                    echo '<br>';
                  echo $tot_laba_kotor;
                    echo '<br>';
                  echo $tot_ebitda;
                    echo '<br>';
                  echo $tot_ebit;
                    echo '<br>';
  



        $bulan = $data['month'];
        return view('dashboard.income_statement', compact(
            'cmb_cabang',
            'sPeriod',
            'ePeriod',
            'tot_pendapatan',
            'tot_ebitda',
            'bulan',
        ));
    } /*}}}*/

}
?>
