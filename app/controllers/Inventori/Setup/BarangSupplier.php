<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class BarangSupplier extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/Setup/BarangSupplierMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_supp = Modules::data_supplier();
        $cmb_supp = $rs_supp->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sSuppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier"');

        return view('inventori.setup.barang_supplier.index', compact(
            'cmb_supp'
        ));
    } /*}}}*/
}
?>