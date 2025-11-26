<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class ManualArPayment extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/PiutangPelanggan/ManualArPaymentMdl');
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

        return view('akunting.piutang_pelanggan.manual_ar_payment.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_cust',
            'cmb_bank',
            'cmb_karyawan'
        ));
    } /*}}}*/

    function cetak ($myid) /*{{{*/
    {
        $rsd = ManualArPaymentMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        $detail_cust = $data_db->suppid == -1 ? '<i style="color: red">[ '.$data_db->detail_cust.' ]</i>' : '';

        $now = date('d-m-Y');

        $rsdaddless = ManualArPaymentMdl::detail_trans_addless($myid);

        $data_addless = !$rsdaddless->EOF ? FieldsToObject($rsdaddless->fields) : New stdClass();


        return view('akunting.piutang_pelanggan.manual_ar_payment.cetak', compact(
            'myid',
            'rsd',
            'data_db',
            'rsdaddless',
            'data_addless',
            'detail_cust',
            'now'
        ));
    } /*}}}*/

   function data_invoice(){

        $custid          = get_var('custid');
        $bank_ar         = get_var('bank_ar');
        $pegawai_id      = get_var('pegawai_id');
        $rand            = rand();
        return view('akunting.piutang_pelanggan.manual_ar_payment.data-invoice', compact(
            'custid',
            'bank_ar',
            'pegawai_id',
            'rand'
        ));
    }

}
?>