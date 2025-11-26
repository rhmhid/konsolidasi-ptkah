<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class ControlPanel extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Konfigurasi/PengaturanDasar/ControlPanelMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_group = ControlPanelMdl::group_configs();
        $cmb_group = $rs_group->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s_cgid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Grup"');

        return view('konfigurasi.pengaturan_dasar.control_panel.index', compact('cmb_group'));
    } /*}}}*/
}
?>