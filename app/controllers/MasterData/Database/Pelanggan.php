<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Pelanggan extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Database/PelangganMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $data_group = PelangganMdl::pelanggan_group();

        return view('master_data.database.pelanggan.index', compact('data_group'));
    } /*}}}*/
}
?>