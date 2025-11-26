<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class ManualAr extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/PiutangPelanggan/ManualArMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {

        $sPeriod = $ePeriod = date('d-m-Y');

        $data_customer = Modules::data_customer();
        $cmb_cust = $data_customer->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCust" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Customer..."');

        $data_bank = Modules::data_bank_cc();
        $cmb_bank = $data_bank->GetMenu2('', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sbank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        $data_karyawan = Modules::data_karyawan();
        $cmb_karyawan = $data_karyawan->GetMenu2('', $data_head->pegawai_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="spegawai_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Karyawan..."');


        return view('akunting.piutang_pelanggan.manual_ar.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_cust',
            'cmb_bank',
            'cmb_karyawan'
        ));
    } /*}}}*/

    function cetak ($myid) /*{{{*/
    {
        $rsd = ManualArMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();


        if($data_db->custid == -1){
            $detail_cust = '<i style="color: red">[ '.$data_db->nama_pegawai.' ]</i>';
        }else if ($data_db->custid == -2){
            $detail_cust = '<i style="color: red">[ '.$data_db->bank_nama.' ]</i>';
        }else{
            $detail_cust = '';
        }


        $now = date('d-m-Y');

        return view('akunting.piutang_pelanggan.manual_ar.cetak', compact(
            'myid',
            'rsd',
            'data_db',
            'detail_cust',
            'now'
        ));
    }

}
?>