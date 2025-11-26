<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class Kasbank extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Database/KasbankMdl');
    } /*}}}*/

    public function index () /*{{{*/
    {
        $rs_bank_type = KasbankMdl::bank_type();
        $cmb_bank_type = $rs_bank_type->GetMenu2('s_bank_type', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank Type" id="s_bank_type"');

        return view('master_data.database.kas_bank.index', compact(
            'cmb_bank_type'
        ));
    } /*}}}*/
}
?>