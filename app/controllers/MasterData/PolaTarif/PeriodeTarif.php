<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PeriodeTarif extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/PolaTarif/PeriodeTarifMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('master_data.pola_tarif.periode_tarif.index');
    } /*}}}*/
}
?>