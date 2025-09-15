<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class KartuPiutang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/PiutangReport/KartuPiutangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_customer = Modules::data_customer();
        $cmb_cust = $data_customer->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCust" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Customer..." required=""');

        $data_bank = Modules::data_bank_cc();
        $cmb_bank = $data_bank->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sbank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        $data_karyawan = Modules::data_karyawan();
        $cmb_karyawan = $data_karyawan->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="spegawai_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Karyawan..."');

        return view('akuntansi_report.piutang_report.kartu_piutang.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_cust',
            'cmb_bank',
            'cmb_karyawan'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'custid'        => get_var('custid'),
            'pegawai_id'    => get_var('pegawai_id'),
            'bank_id'       => get_var('bank_id'),
        );

        $sdate = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $edate = dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $data['status'] = 'saldo_awal';

        $rs_awal = KartuPiutangMdl::list($data);

        $saldo_awal = $rs_awal->fields['saldo_awal'];

        $data['status'] = '';

        $rs = KartuPiutangMdl::list($data);

        $data_cust = Modules::GetCustomer($data['custid']);

        $nama_customer = $data_cust['nama_customer'];

        if ($data['custid'] == -1)
        {
            $data_emp = Modules::GetPerson($data['pegawai_id']);

            $nama_customer .= ' <i style="color: red">[ '.$data_emp['nama_lengkap'].' ]</i>';
        }
        elseif ($data['custid'] == -2)
        {
            $data_bank = Modules::GetBank($data['bank_id']);

            $nama_customer .= ' <i style="color: red">[ '.$data_bank['bank_nama'].' ]</i>';
        }

        return view('akuntansi_report.piutang_report.kartu_piutang.cetak', compact(
            'data',
            'sdate',
            'edate',
            'saldo_awal',
            'rs',
            'nama_customer'
        ));
    } /*}}}*/
}
?>