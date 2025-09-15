<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class HakAkses extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('UserManagement/AksesAplikasi/HakAksesMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('user_management.akses_aplikasi.hak_akses.index');
    } /*}}}*/
}
?>