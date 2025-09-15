<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class AgingPiutang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/PiutangReport/AgingPiutangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sdate = date('d-m-Y');

        $data_customer = Modules::data_customer();
        $cmb_cust = $data_customer->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCust" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Customer..."');

        $data_bank = Modules::data_bank_cc();
        $cmb_bank = $data_bank->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sbank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        $data_karyawan = Modules::data_karyawan();
        $cmb_karyawan = $data_karyawan->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="spegawai_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Karyawan..."');

        return view('akuntansi_report/piutang_report/aging_piutang.list', compact(
            'sdate',
            'cmb_cust',
            'cmb_bank',
            'cmb_karyawan'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'custid'        => get_var('custid'),
<<<<<<< Updated upstream
            'bank_id'       => get_var('bank_id'),
            'pegawai_id'    => get_var('pegawai_id'),
=======
            'bank_id'      => get_var('bank_id'),
            'pegawai_id'   => get_var('pegawai_id'),
>>>>>>> Stashed changes
        );

        $sdate = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $rs = AgingPiutangMdl::list($data);

        return view('akuntansi_report/piutang_report/aging_piutang.cetak', compact(
            'data',
            'sdate',
            'rs',
        ));
    } /*}}}*/
}
?>
