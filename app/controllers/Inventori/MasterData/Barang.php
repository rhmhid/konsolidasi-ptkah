<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Barang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/BarangMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_kel_brg = Modules::data_kel_brg();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('s_kbid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s_kbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Kategori Barang"');

        return view('inventori.masterdata.barang.index', compact(
            'cmb_kel_brg'
        ));
    } /*}}}*/
}
?>