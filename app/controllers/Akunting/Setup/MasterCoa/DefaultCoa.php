<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class DefaultCoa extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/DefaultCoaMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('akunting.setup.master_coa.default_coa.index');
    } /*}}}*/
}
?>