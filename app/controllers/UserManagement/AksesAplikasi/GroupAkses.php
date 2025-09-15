<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class GroupAkses extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('UserManagement/AksesAplikasi/GroupAksesMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('user_management.akses_aplikasi.group_akses.index');
    } /*}}}*/
}
?>