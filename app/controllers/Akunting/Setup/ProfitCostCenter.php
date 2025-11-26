<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class ProfitCostCenter extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/ProfitCostCenterMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('akunting.setup.profit_cost_center.index');
    } /*}}}*/
}
?>