<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PeriodeAkunting extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/PeriodeAkuntingMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('akunting.setup.periode_akunting.index');
    } /*}}}*/
}
?>