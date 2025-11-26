<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PembayaranInvoice extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/PembayaranInvoiceMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sSuppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..."');

        $data_doctor = Modules::data_doctor();
        $cmb_doctor = $data_doctor->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sDoctorid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Dokter..."');

        return view('akunting.hutang_supplier.pembayaran_invoice.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_supp',
            'cmb_doctor'
        ));
    } /*}}}*/

    function cetak_bukti ($myid) /*{{{*/
    {
        $rsd = PembayaranInvoiceMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        $rs_addless = PembayaranInvoiceMdl::detail_addless($myid);

        $data_al = !$rs_addless->EOF ? FieldsToObject($rs_addless->fields) : New stdClass();

        $now = date('d-m-Y');

        return view('akunting.hutang_supplier.pembayaran_invoice.cetak_bukti', compact(
            'myid',
            'rsd',
            'data_db',
            'rs_addless',
            'data_al',
            'now'
        ));
    } /*}}}*/
}
?>