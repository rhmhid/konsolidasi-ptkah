<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PosNeraca extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/PosisiLaporan/PosNeracaMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_jenis_pos = PosNeracaMdl::jenis_pos();
        $cmb_jenis_pos = $rs_jenis_pos->GetMenu2('s_jenis_pos', 1, false, false, 0, 'class="form-select form-select-sm rounded-1" id="s_jenis_pos" data-control="select2" data-hide-search="true" data-width="200px"');

        return view('akunting.setup.posisi_laporan.pos_neraca.index', compact('cmb_jenis_pos'));
    } /*}}}*/
}
?>