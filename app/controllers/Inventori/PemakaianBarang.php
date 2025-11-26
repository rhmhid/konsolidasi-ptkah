<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PemakaianBarang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/PemakaianBarangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $rs_gudang = Modules::data_gudang2();
        $cmb_gudang_from = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        $rs_gudang->MoveFirst();
        $cmb_gudang_to = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sReffGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        $rs_kel_brg = Modules::data_kel_brg3();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sKbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        return view('inventori.pemakaian_barang.list', compact(
            'cmb_gudang_from',
            'cmb_gudang_to',
            'cmb_kel_brg'
        ));
    } /*}}}*/

    public function cetak ($myid) /*{{{*/
    {
        $rsd = PemakaianBarangMdl::detail_data($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();
        $now = dbtstamp2stringina(date('Y-m-d'));

        return view('inventori.pemakaian_barang.cetak', compact(
            'myid',
            'rsd',
            'data_db',
            'now'
        ));
    } /*}}}*/
}
?>