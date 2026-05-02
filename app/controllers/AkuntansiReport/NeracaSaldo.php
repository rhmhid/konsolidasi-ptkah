<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class NeracaSaldo extends BaseController
{
    static $ho_jkk, $ho_kah;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaSaldoMdl');

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');

        self::$ho_kah = dataConfigs('default_kode_branch_kah');
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

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $empty_tb = true;
        $data_tb = $data_sum = [];

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];
            $data['sdate']  = $data['year'].'-'.$data['month'].'-01';
            $data['edate']  = date('Y-m-t', strtotime($data['sdate']));

            $rs = NeracaSaldoMdl::list($data);

            $data_db = array();
            while (!$rs->EOF)
            {
                $coacode = $rs->fields['coacode'];
                // $branch_code = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                if ($data['bid'] == -1 && $rs->fields['kdbid'] == 2) $branch_code = self::$ho_jkk;
                elseif ($data['bid'] == -1 && $rs->fields['kdbid'] == 3) $branch_code = self::$ho_kah;
                else $branch_code = $rs->fields['branch_code'];

                if (!isset($data_db[$coacode]))
                {
                    $data_db[$coacode] = array(
                        'coaid'         => $rs->fields['coaid'],
                        'coaname'       => $rs->fields['coaname'],
                        'default_debet' => $rs->fields['default_debet']
                    );
                }

                $data_db[$coacode]['branch'][$branch_code] = [
                    'openingbal'    => floatval($rs->fields['openingbal']),
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'closingbal'    => floatval($rs->fields['closingbal']),
                ];

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);
            $coaid_laba_periode_lalu = Modules::$laba_periode_lalu;

            while (!$rss->EOF)
            {
                $coacode = $rss->fields['coacode'];
                // $branch_code = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];
                if ($data['bid'] == -1 && $rss->fields['kdbid'] == 2) $branch_code = self::$ho_jkk;
                elseif ($data['bid'] == -1 && $rss->fields['kdbid'] == 3) $branch_code = self::$ho_kah;
                else $branch_code = $rss->fields['branch_code'];

                if ($rss->fields['coaid'] == $coaid_laba_periode_lalu)
                {
                    if (!isset($data_db[$coacode]))
                    {
                        $data_db[$coacode] = array(
                            'coaid'         => $rss->fields['coaid'],
                            'coaname'       => $rss->fields['coaname'],
                            'default_debet' => $rss->fields['default_debet']
                        );
                    }

                    if (isset($data_db[$coacode]['branch'][$branch_code]))
                        $data_db[$coacode]['branch'][$branch_code]['openingbal'] += floatval($rss->fields['openingbal']);
                    else
                    {
                        $data_db[$coacode]['branch'][$branch_code] = [
                            'openingbal'    => floatval($rss->fields['openingbal']),
                            'debet'         => 0,
                            'credit'        => 0,
                            'closingbal'    => 0,
                        ];
                    }
                }

                $rss->MoveNext();
            }

            ksort($data_db);

            foreach ($data_cabang as $bcode => $cab)
                $data_sum[$bcode] = ['debet' => 0, 'credit' => 0];

            if (!empty($data_db))
            {
                $no = 1;
                $empty_tb = false;

                foreach ($data_db as $coacode => $tmp)
                {
                    $row = [
                        'no'      => $no++,
                        'coaid'   => $tmp['coaid'],
                        'coacode' => $coacode,
                        'coaname' => $tmp['coaname'],
                        'posisi'  => $tmp['default_debet'] == 't' ? 'Dr' : 'Cr',
                        'branch'  => [],
                        'opening' => 0,
                        'debet'   => 0,
                        'credit'  => 0,
                        'balance' => 0
                    ];

                    foreach ($data_cabang as $bcode => $cab)
                    {
                        if (isset($tmp['branch'][$bcode]))
                        {
                            $b = $tmp['branch'][$bcode];
                            $balance = $tmp['default_debet'] == 't' ? ($b['debet'] - $b['credit']) : ($b['credit'] - $b['debet']);
                            $balance += $b['openingbal'];

                            $row['branch'][$bcode] = [
                                'opening' => $b['openingbal'],
                                'debet'   => $b['debet'],
                                'credit'  => $b['credit'],
                                'balance' => $balance
                            ];

                            $data_sum[$bcode]['debet'] += $b['debet'];
                            $data_sum[$bcode]['credit'] += $b['credit'];
                        }
                        else
                        {
                            $row['branch'][$bcode] = [
                                'opening' => 0,
                                'debet'   => 0,
                                'credit'  => 0,
                                'balance' => 0
                            ];
                        }
                    }

                    $data_tb[] = $row;
                }
            }
        }

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        return view('akuntansi_report.neraca_saldo.cetak', compact(
            'cabang',
            'data',
            'report_month',
            'data_cabang',
            'data_tb',
            'data_sum'
        ));
    } /*}}}*/
}
?>