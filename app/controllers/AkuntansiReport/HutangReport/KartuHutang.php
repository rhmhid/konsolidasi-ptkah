<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class KartuHutang extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/HutangReport/KartuHutangMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..." required=""');

        $data_doctor = Modules::data_doctor();
        $cmb_doctor = $data_doctor->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="doctor-id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Dokter..."');

        return view('akuntansi_report/hutang_report/kartu_hutang.list', compact(
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

        $data['status'] = 'saldo_awal';

        $rs_awal = KartuHutangMdl::list($data);

        $saldo_awal = $rs_awal->fields['saldo_awal'];

        $data['status'] = '';

        $rs = KartuHutangMdl::list($data);

        $data_supp = Modules::GetSupplier($data['suppid']);

        $nama_supp = $data_supp['nama_supp'];

        if ($data['suppid'] == -1)
        {
            $data_doctor = Modules::GetPerson($data['doctor_id']);

            $nama_supp .= ' <i style="color: red">[ '.$data_doctor['nama_lengkap'].' ]</i>';
        }

        return view('akuntansi_report/hutang_report/kartu_hutang.cetak', compact(
            'data',
            'sdate',
            'edate',
            'saldo_awal',
            'rs',
            'nama_supp'
        ));
    } /*}}}*/
}
?>