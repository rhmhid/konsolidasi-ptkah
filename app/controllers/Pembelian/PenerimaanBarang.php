<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PenerimaanBarang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Pembelian/PenerimaanBarangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $rs_gudang = Modules::data_gudang_besar();
        $cmb_gudang = $rs_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_supplier = Modules::data_supplier();
        $cmb_supplier = $rs_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sSuppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('pembelian.penerimaan_barang.list', compact(
            'cmb_gudang',
            'cmb_supplier',
        ));
    } /*}}}*/

    public function cetak ($myid) /*{{{*/
    {
        $rsd = PenerimaanBarangMdl::detail_data($myid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();
        $now = dbtstamp2stringina(date('Y-m-d'));

        return view('pembelian.penerimaan_barang.cetak', compact(
            'myid',
            'rsd',
            'data_db',
            'now'
        ));
    } /*}}}*/
}
?>