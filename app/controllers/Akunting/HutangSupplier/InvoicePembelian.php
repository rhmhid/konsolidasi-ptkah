<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class InvoicePembelian extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/InvoicePembelianMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sSuppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..."');

        return view('akunting.hutang_supplier.invoice_pembelian.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_supp'
        ));
    } /*}}}*/

    function cetak_voucher ($myid) /*{{{*/
    {
        $rsd = InvoicePembelianMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        $now = date('d-m-Y');

        return view('akunting.hutang_supplier.invoice_pembelian.cetak_voucher', compact(
            'myid',
            'rsd',
            'data_db',
            'now'
        ));
    } /*}}}*/

    function cetak_faktur ($myid) /*{{{*/
    {
        $rsd = InvoicePembelianMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        $now = date('d-m-Y');

        return view('akunting.hutang_supplier.invoice_pembelian.cetak_faktur', compact(
            'myid',
            'rsd',
            'data_db',
            'now'
        ));
    } /*}}}*/
}
?>