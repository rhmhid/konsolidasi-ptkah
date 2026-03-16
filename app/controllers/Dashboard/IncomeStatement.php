<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class IncomeStatement extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/DaftarJurnalMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sBid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');
	    
        return view('dashboard.income_statement', compact(
            'cmb_cabang',
        ));
    } /*}}}*/

}
?>
