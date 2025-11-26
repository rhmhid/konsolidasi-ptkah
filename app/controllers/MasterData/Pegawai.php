<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Pegawai extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/PegawaiMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('master_data.pegawai.index');
    } /*}}}*/
}
?>