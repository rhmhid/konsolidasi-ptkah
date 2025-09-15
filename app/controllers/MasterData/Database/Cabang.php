<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Cabang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Database/CabangMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_tipe = CabangMdl::branch_tipe();
        $cmb_tipe = $rs_tipe->GetMenu2('s_btid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s_btid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Tipe"');

        $rs_wilayah = CabangMdl::branch_wilayah();
        $cmb_wilayah = $rs_wilayah->GetMenu2('s_bwid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s_bwid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Wilayah"');

        return view('master_data.database.cabang.index', compact(
            'cmb_tipe',
            'cmb_wilayah'
        ));
    } /*}}}*/
}
?>