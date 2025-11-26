<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class MataUang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MataUangMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('akunting.setup.mata_uang.index');
    } /*}}}*/
}
?>