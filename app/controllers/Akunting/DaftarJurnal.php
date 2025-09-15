<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class DaftarJurnal extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/DaftarJurnalMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_tipe_jurnal = Modules::data_tipe_jurnal();
        $cmb_tipe_jurnal = $data_tipe_jurnal->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sJtid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $data_posted = Modules::data_posted();
        $cmb_posted = $data_posted->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sPosted" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('akunting.daftar_jurnal.list', compact(
            'cmb_tipe_jurnal',
            'cmb_posted'
        ));
    } /*}}}*/

    public function cetak ($myglid) /*{{{*/
    {
        $rsd = DaftarJurnalMdl::detail_jurnal($myglid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();
        $now = dbtstamp2stringina(date('Y-m-d'));

        return view('akunting.daftar_jurnal.cetak', compact(
            'myglid',
            'rsd',
            'data_db',
            'now'
        ));
    } /*}}}*/
}
?>