<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class GroupTarif extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/PolaTarif/GroupTarifMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        return view('master_data.pola_tarif.group_tarif.index');
    } /*}}}*/
}
?>