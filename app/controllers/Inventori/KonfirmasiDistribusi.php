<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class KonfirmasiDistribusi extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/KonfirmasiDistribusiMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $rs_gudang = Modules::data_gudang2();
        $cmb_gudang_from = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        $rs_gudang->MoveFirst();
        $cmb_gudang_to = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sReffGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        $rs_kel_brg = Modules::data_kel_brg3();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sKbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        $rs_konfirmasi = Modules::data_konfirmasi();
        $cmb_konfirmasi = $rs_konfirmasi->GetMenu2('', 'f', false, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sKonfirm" data-control="select2" data-placeholder="Pilih..." ');

        return view('inventori.konfirmasi_distribusi.list', compact(
            'cmb_gudang_from',
            'cmb_gudang_to',
            'cmb_kel_brg',
            'cmb_konfirmasi'
        ));
    } /*}}}*/
}
?>