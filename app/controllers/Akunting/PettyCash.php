<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PettyCash extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/PettyCashMdl');
    } /*}}}*/

    public function type () /*{{{*/
    {
        $rs_coa = PettyCashMdl::data_coa();
        $cmb_coa = $rs_coa->GetMenu2('', $data_kate->coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCoaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('akunting.petty_cash.type', compact('cmb_coa'));
    } /*}}}*/

    public function list () /*{{{*/
    {
        $rs_cash_book = PettyCashMdl::data_cash_book();
        $cmb_cash_book = $rs_cash_book->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sBank" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." ');

        return view('akunting.petty_cash.list', compact('cmb_cash_book'));
    } /*}}}*/

    public function cetak ($myid) /*{{{*/
    {
        $rsd = PettyCashMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        return view('akunting.petty_cash.cetak', compact(
            'myid',
            'rsd',
            'data_db'
        ));
    } /*}}}*/
}
?>