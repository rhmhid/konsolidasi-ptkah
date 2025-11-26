<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class OtorisasiAkses extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Konfigurasi/PengaturanDasar/OtorisasiAksesMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_group = Modules::data_group_otorisasi();
        $cmb_group = $rs_group->GetMenu2('s_otogid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s_otogid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Group Otorisasi"');

        return view('konfigurasi.pengaturan_dasar.otorisasi_akses.index', compact(
            'cmb_group'
        ));
    } /*}}}*/
}
?>