<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PenerimaanBarang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Pembelian/Report/PenerimaanBarangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_gudang = Modules::data_gudang2();
        $cmb_gudang = $data_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sSuppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('pembelian.report.penerimaan_barang.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_gudang',
            'cmb_supp'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'         => get_var('sdate'),
            'edate'         => get_var('edate'),
            'gid'           => get_var('gid'),
            'suppid'        => get_var('suppid'),
            'kode_nama_brg' => get_var('kode_nama_brg'),
        );

        $rs = PenerimaanBarangMdl::list($data);

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' s/d '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        return view('pembelian.report.penerimaan_barang.cetak', compact(
            'data',
            'rs',
            'periode'
        ));
    } /*}}}*/
}
?>