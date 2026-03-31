<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class HutangSupplier extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/SummaryReport/HutangSupplierMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sBid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('akuntansi_report.summary_report.hutang_supplier.list', compact(
            'cmb_cabang'
        ));
    } /*}}}*/

    // public function cetak ($myglid, $mybid) /*{{{*/
    // {
    //     $rsd = HutangSupplierMdl::detail_jurnal($myglid, $mybid);

    //     $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();
    //     $now = dbtstamp2stringina(date('Y-m-d'));

    //     return view('akunting.daftar_jurnal.cetak', compact(
    //         'myglid',
    //         'mybid',
    //         'rsd',
    //         'data_db',
    //         'now'
    //     ));
    // } /*}}}*/
}
?>