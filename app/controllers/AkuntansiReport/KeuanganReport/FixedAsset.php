<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class FixedAsset extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/KeuanganReport/FixedAssetMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_kategori_fa = Modules::data_kategori_fa();
        $cmb_kategori_fa = $data_kategori_fa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sFacid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Kategori Asset..."');

        $data_lokasi_fa = Modules::data_lokasi_fa();
        $cmb_lokasi_fa = $data_lokasi_fa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sFalid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Lokasi Asset..."');

        return view('akuntansi_report.keuangan_report.fixed_asset.list', compact(
            'cmb_kategori_fa',
            'cmb_lokasi_fa'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'smonth'            => get_var('smonth', date('m')),
            'syear'             => get_var('syear', date('Y')),
            'facid'             => get_var('facid'),
            'falid'             => get_var('falid'),
            'kode_nama_desc'    => get_var('kode_nama_desc')
        );

        $rs = FixedAssetMdl::list($data);

        return view('akuntansi_report.keuangan_report.fixed_asset.cetak', compact(
            'data',
            'rs'
        ));
    } /*}}}*/
}
?>