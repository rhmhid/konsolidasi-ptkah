<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Cek_koneksi extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Cekkoneksimdl');
    } /*}}}*/

    public function index () /*{{{*/
    {


        $rs = Cekkoneksimdl::list();


        $hasil_test = '';
        while (!$rs->EOF)
        {
            $bid                = $rs->fields['bid'];
            $branch_name        = $rs->fields['branch_name'];
            $cabang_url         = $rs->fields['cabang_url'];

            $this->_test_koneksi($cabang_url);
            $data_test     = $this->_test_koneksi($cabang_url);

            $statusClass = ($data_test['http']['code'] == 200) ? 'success' : 'danger';
            $statusText  = ($data_test['http']['code'] == 200) ? 'ONLINE' : 'OFFLINE';

            $hasil_test .= '<div class="card card-flush shadow-sm mt-2">
                                <div class="card-header mb-0">
                                    <h3 class="card-title">
                                        <i class="bi bi-hdd-network text-primary me-2"></i>
                                        '.$branch_name.'
                                    </h3>
                                    <div class="card-toolbar">
                                        <span class="badge badge-light-'.$statusClass.'">
                                            '.$statusText.'
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body mt-0">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <strong>URL : </strong><br>
                                            <span class="text-gray-700">'.$cabang_url.'</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <strong>HTTP Status:</strong><br>
                                            <span class="badge badge-light-'.$statusClass.'">
                                                '.$data_test['http']['code'].'
                                            </span>
                                        </div>

                                        <div class="col-sm-3">
                                            <strong>Waktu Akses:</strong><br>
                                            <span class="badge badge-light-'.$statusClass.'">
                                                '.$data_test['akses']['total_ms'].' ms
                                            </span>
                                        </div>

                                        <div class="col-sm-3">
                                            <strong>Speed Transfer:</strong><br>
                                            <span class="badge badge-light-'.$statusClass.'">
                                                '.$data_test['transfer']['speed_kbps'].' KB/s
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>';

            $rs->MoveNext();
        }

        return view('master_data.cek_koneksi.index', compact(
            'hasil_test'
        ));
         
    } /*}}}*/

    function _test_koneksi($url){

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,   // WAJIB: ambil body
            CURLOPT_HEADER         => false,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $body  = curl_exec($ch);
        $error = curl_error($ch);
        $info  = curl_getinfo($ch);
        curl_close($ch);

        /* =========================
           HITUNG SPEED (REAL)
        ========================= */
        $downloadBytes = $info['size_download'];     // bytes
        $totalTime     = $info['total_time'];        // seconds

        $speedBytes = ($totalTime > 0)
            ? $downloadBytes / $totalTime
            : 0;

        $response = [
            'status' => empty($error) ? 'OK' : 'ERROR',
            'error'  => $error ?: null,

            'http' => [
                'code' => $info['http_code'],
            ],

            // SEMUA WAKTU DALAM MILLISECOND
            'akses' => [
                'connect_ms' => round($info['connect_time'] * 1000, 2),
                'ttfb_ms'    => round($info['starttransfer_time'] * 1000, 2),
                'total_ms'   => round($info['total_time'] * 1000, 2),
            ],

            // TRANSFER
            'transfer' => [
                'download_kb' => round($downloadBytes / 1024, 2),
                'speed_kbps'  => round($speedBytes / 1024, 2),
            ],
        ];
        return $response;

    }
}
?>