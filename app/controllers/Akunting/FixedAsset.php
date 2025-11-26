<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class FixedAsset extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/FixedAssetMdl');
    } /*}}}*/

    public function lokasi () /*{{{*/
    {
        return view('akunting.fixed_asset.lokasi');
    } /*}}}*/

    public function kategori () /*{{{*/
    {
        return view('akunting.fixed_asset.kategori');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_kategori_fa = Modules::data_kategori_fa();
        $cmb_kategori_fa = $data_kategori_fa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sFacid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Kategori Asset..."');

        $data_lokasi_fa = Modules::data_lokasi_fa();
        $cmb_lokasi_fa = $data_lokasi_fa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sFalid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Lokasi Asset..."');

        $data_status_fa = Modules::data_status_fa();
        $cmb_status_fa = $data_status_fa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sFAStatus" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Status Asset..."');

        return view('akunting.fixed_asset.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_kategori_fa',
            'cmb_lokasi_fa',
            'cmb_status_fa'
        ));
    } /*}}}*/

    public function depresiasi () /*{{{*/
    {
        $trans_date = date('m-Y');

        $data_kategori_fa = Modules::data_kategori_fa();
        $cmb_kategori_fa = $data_kategori_fa->GetMenu2('facid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="facid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Kategori Asset..."');

        return view('akunting.fixed_asset.depresiasi', compact(
            'trans_date',
            'cmb_kategori_fa'
        ));
    } /*}}}*/
}
?>