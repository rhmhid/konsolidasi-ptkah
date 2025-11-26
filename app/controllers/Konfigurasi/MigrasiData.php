<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

use Luthier\Auth;
use PHPJasper\PHPJasper;

class MigrasiData extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Konfigurasi/MigrasiDataMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('konfigurasi.migrasi_data.index');
    } /*}}}*/

    public function download_file ($xls) /*{{{*/
    {
        $xls_filename = basename($xls);

        header('content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename='.$xls_filename.'.xls');

        $fp = fopen(dirname(__FILE__)."/files/$xls_filename.xls","rb") or exit;

        while (!feof($fp))
        {
            echo(fread($fp,1024));
        }

        fclose($fp);
        exit;
    } /*}}}*/

    public function reset_data () /*{{{*/
    {
        return view('konfigurasi.migrasi_data.reset_data');
    } /*}}}*/

    public function updol_pegawai () /*{{{*/
    {
        return view('konfigurasi.migrasi_data.updol_pegawai');
    } /*}}}*/

    public function updol_user () /*{{{*/
    {
        return view('konfigurasi.migrasi_data.updol_user');
    } /*}}}*/

    public function download_akses ($type)
    {
        header('content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="user_akses.csv"');

        $separator = ";";
        $hdr = array("LoginID", "PID", "NIP", "Nama Pegawai", "Login Name", "Login Pass", "Group Akses");

        echo $this->bscsvutil->arrayToCsvString($hdr, $separator)."\r\n";

        $rs = MigrasiDataMdl::list_user_akses($type);

        while (!$rs->EOF)
        {
            $arr = array();
            $data_group = "";
            $arr_group = $rs->fields['user_group'] ? explode(',', $rs->fields['user_group']) : [];

            if (is_array($arr_group))
            {
                foreach ($arr_group as $k => $v)
                    $data_group .= "".$v.'::';
            }

            $arr[] = $rs->fields['asid'];
            $arr[] = $rs->fields['pid'];
            $arr[] = $rs->fields['nip'];
            $arr[] = $rs->fields['nama_lengkap'];
            $arr[] = $rs->fields['username'];
            $arr[] = "";
            $arr[] = $data_group;

            echo $this->bscsvutil->arrayToCsvString($arr, $separator)."\n";

            $rs->MoveNext();
        }

        exit;
    }

    public function jasper_statis () /*{{{*/
    {
        Myjasper::print_pdf('hello_world', [], true, false);
        exit();
    } /*}}}*/

    public function jasper_statis_ori () /*{{{*/
    {
        $input = VIEWPATH . 'jasper_files/hello_world.jasper';  
        $output = VIEWPATH . 'jasper_files/temp';    
        $file = VIEWPATH . 'jasper_files/temp/hello_world.pdf';

        $options = [ 
            'format' => ['pdf', 'rtf'] 
        ];

        $jasper = new PHPJasper;

        $jasper->process(
            $input,
            $output,
            $options
        )->execute();

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $file . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        @flush();
        @readfile($file);
        @unlink($file);
        exit();
    } /*}}}*/

    public function jasper_json () /*{{{*/
    {
        $params = [
            'myString'  => 'myString',
            'myInt'     => 'myInt',
            'myDate'    => 'myDate',
            'myImage'   => 'assets/media/logos/default.svg',
        ];

        Myjasper::print_pdf_json('hello_world_params', $params, $json_data, $data_query, true, false, 'pdf');
        exit();
    } /*}}}*/

    public function import_tb () /*{{{*/
    {
        $now = date('d-m-Y H:i');

        $data_coa = Modules::data_coa();
        $cmb_coa = $data_coa->GetMenu2('coaid', Modules::$akun_migrasi, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih COA" required=""');

        return view('konfigurasi.migrasi_data.akunting.import_tb', compact(
            'now',
            'cmb_coa'
        ));
    } /*}}}*/

    public function balance_ledger_tb () /*{{{*/
    {
        return view('konfigurasi.migrasi_data.akunting.balance_ledger_tb');
    } /*}}}*/

    public function import_barang () /*{{{*/
    {
        // phpinfo();
        // die();
        return view('konfigurasi.migrasi_data.import_barang');
    } /*}}}*/

    public function import_stok () /*{{{*/
    {
        $now = date('d-m-Y H:i');
        $data_coa = Modules::data_coa();
        $cmb_coa = $data_coa->GetMenu2('coaid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih COA" required=""');

        $data_gudang = Modules::data_gudang();
        $cmb_gudang = $data_gudang->GetMenu2('gid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="gid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Gudang" required=""');

        return view('konfigurasi.migrasi_data.import_stok', compact(
            'now',
            'cmb_coa',
            'cmb_gudang'
        ));
    } /*}}}*/

    public function import_manual_ap () /*{{{*/
    {
        $now = date('d-m-Y H:i');

        $data_coa = Modules::data_coa();
        $cmb_coa = $data_coa->GetMenu2('coaid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih COA" required=""');

        return view('konfigurasi.migrasi_data.akunting.import_manual_ap', compact(
            'now',
            'cmb_coa'
        ));
    } /*}}}*/

}
?>
