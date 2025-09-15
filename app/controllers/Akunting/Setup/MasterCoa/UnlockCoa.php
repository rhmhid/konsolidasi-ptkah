<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class UnlockCoa extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/UnlockCoaMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('akunting.setup.master_coa.unlock_coa.index');
    } /*}}}*/
}
?> 