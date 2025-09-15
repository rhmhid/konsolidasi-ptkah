<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class ManualAp extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/ManualApMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sSuppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..."');

        $data_doctor = Modules::data_doctor();
        $cmb_doctor = $data_doctor->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sDoctorid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Dokter..."');

        return view('akunting.hutang_supplier.manual_ap.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_supp',
            'cmb_doctor'
        ));
    } /*}}}*/

    function cetak ($myid) /*{{{*/
    {
        $rsd = ManualApMdl::detail_trans($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        $nama_dokter = $data_db->suppid == -1 ? '<i style="color: red">[ '.$data_db->nama_dokter.' ]</i>' : '';

        $now = date('d-m-Y');

        return view('akunting.hutang_supplier.manual_ap.cetak', compact(
            'myid',
            'rsd',
            'data_db',
            'nama_dokter',
            'now'
        ));
    } /*}}}*/
}
?>