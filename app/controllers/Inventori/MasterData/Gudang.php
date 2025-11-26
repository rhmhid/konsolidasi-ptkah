<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Gudang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/GudangMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_jenis = GudangMdl::jenis_gudang();
        $cmb_jenis = $rs_jenis->GetMenu2('s_jenis_gudang', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s_jenis_gudang" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Jenis Gudang"');

        return view('inventori.masterdata.gudang.index', compact('cmb_jenis'));
    } /*}}}*/
}
?>