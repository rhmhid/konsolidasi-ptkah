<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class InfoStok extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/Report/InfoStokMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_coa_inv = Modules::setup_coa_inv();
        $cmb_coa_inv = $data_coa_inv->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCoaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $data_kel_brg = Modules::data_kel_brg();
        $cmb_kel_brg = $data_kel_brg->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sKbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $data_gudang = Modules::data_gudang2();
        $cmb_gudang = $data_gudang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sGid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('inventori.report.info_stok.list', compact(
            'cmb_coa_inv',
            'cmb_kel_brg',
            'cmb_gudang',
            'KodeNama'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'     => get_var('sdate'),
            'coaid_inv' => get_var('coaid_inv'),
            'kbid'      => get_var('kbid'),
            'gid'       => get_var('gid'),
            'kode_nama' => get_var('kode_nama'),
        );

        $rs = InfoStokMdl::list($data);

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        return view('inventori.report.info_stok.cetak', compact(
            'data',
            'rs',
            'periode'
        ));
    } /*}}}*/

    public function detail_stok () /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate'),
            'mbid'  => get_var('mbid')
        );

        $rs = InfoStokMdl::detail_stok($data);

        $nama_brg = $rs->fields['nama_brg'];

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        return view('inventori.report.info_stok.detail_stok', compact(
            'data',
            'rs',
            'nama_brg',
            'periode'
        ));
    } /*}}}*/
}
?>