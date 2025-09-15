<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class MutasiSaldo extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/MutasiSaldoMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_bank = Modules::data_bank();
        $cmb_bank_from = $data_bank->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sBank-From" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        $data_bank->MoveFirst();
        $cmb_bank_to = $data_bank->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sBank-To" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        return view('akunting.mutasi_saldo.list', compact(
            'cmb_bank_from',
            'cmb_bank_to'
        ));
    } /*}}}*/

    public function cetak ($myid) /*{{{*/
    {
        $rsd = MutasiSaldoMdl::detail_mutasi($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();
        $now = dbtstamp2stringina(date('Y-m-d'));

        return view('akunting.mutasi_saldo.cetak', compact(
            'data_db',
            'now'
        ));
    } /*}}}*/
}
?>