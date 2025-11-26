<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Pospl extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/PosisiLaporan/PosplMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_pos = PosplMdl::pos();
        $cmb_pos = $rs_pos->GetMenu2('sPos', 2, false, false, 0, 'class="form-select form-select-sm rounded-1" id="s-pos" data-control="select2" data-hide-search="true" data-width="200px"');

        return view('akunting.setup.posisi_laporan.pos_pl.index', compact(
            'cmb_pos'
        ));
    } /*}}}*/

    public function index_rekap () /*{{{*/
    {
        $rs_pos = PosplMdl::pos();
        $cmb_pos = $rs_pos->GetMenu2('sPos', 1, false, false, 0, 'class="form-select form-select-sm rounded-1" id="s-pos" data-control="select2" data-hide-search="true" data-width="200px"');

        return view('akunting.setup.posisi_laporan.pos_pl.index_rekap', compact(
            'cmb_pos'
        ));
    } /*}}}*/
}
?>