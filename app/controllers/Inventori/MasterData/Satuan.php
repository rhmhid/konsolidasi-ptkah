<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Satuan extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/SatuanMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('inventori.masterdata.satuan.index');
    } /*}}}*/
}
?>