<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class StockAdjusment extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/ManajemenStok/StockAdjusmentMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $rs_gudang = Modules::data_gudang2();
        $cmb_gudang = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        $rs_kel_brg = Modules::data_kel_brg2();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sKbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        return view('inventori.manajemen_stok.stock_adjusment.list', compact(
            'cmb_gudang',
            'cmb_kel_brg'
        ));
    } /*}}}*/

    public function stock () /*{{{*/
    {
        $rs_gudang = Modules::data_gudang2();
        $cmb_gudang = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_kel_brg = Modules::data_kel_brg2();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sKbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        return view('inventori.manajemen_stok.stock_adjusment.stock', compact(
            'cmb_gudang',
            'cmb_kel_brg'
        ));
    } /*}}}*/
}
?>