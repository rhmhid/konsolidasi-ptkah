<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class KartuStok extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/Report/KartuStokMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_gudang = Modules::data_gudang2();
        $cmb_gudang = $data_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $data_barang = Modules::data_barang();
        $cmb_barang = $data_barang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sMbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        return view('inventori.report.kartu_stok.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_gudang',
            'cmb_barang'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate'),
            'edate' => get_var('edate'),
            'gid'   => get_var('gid'),
            'mbid'  => get_var('mbid'),
        );

        $awal = KartuStokMdl::stock_awal($data);

        $rs = KartuStokMdl::list($data);

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' s/d '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $data_gudang = Modules::GetGudang($data['gid']);
        $gudang = $data_gudang['nama_gudang'];

        $data_barang = Modules::Getbarang($data['mbid']);
        $barang = $data_barang['kode_brg'].' - '.$data_barang['nama_brg'];

        return view('inventori.report.kartu_stok.cetak', compact(
            'data',
            'awal',
            'rs',
            'periode',
            'gudang',
            'barang'
        ));
    } /*}}}*/
}
?>