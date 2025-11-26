<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class PembayaranHutang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/HutangReport/PembayaranHutangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..."');

        $data_doctor = Modules::data_doctor();
        $cmb_doctor = $data_doctor->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="doctor-id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Dokter..."');

        return view('akuntansi_report/hutang_report/pembayaran_hutang.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_supp',
            'cmb_doctor'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'suppid'    => get_var('suppid'),
            'doctor_id' => get_var('doctor_id'),
        );

        $sdate = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $edate = dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $rs = PembayaranHutangMdl::list($data);

        return view('akuntansi_report/hutang_report/pembayaran_hutang.cetak', compact(
            'data',
            'sdate',
            'edate',
            'rs'
        ));
    } /*}}}*/
}
?>