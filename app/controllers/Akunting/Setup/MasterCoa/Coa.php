<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Coa extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/CoaMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_type = CoaMdl::list_coa_type();
        $cmb_type = $rs_type->GetMenu2('s_coatid', 1, false, false, 0, 'class="form-select form-select-sm rounded-1" id="s_coatid" data-control="select2" data-hide-search="true" data-width="200px"');

        return view('akunting.setup.master_coa.coa.index', compact('cmb_type'));
    } /*}}}*/
}
?>