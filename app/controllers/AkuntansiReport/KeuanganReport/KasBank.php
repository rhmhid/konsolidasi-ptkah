<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class KasBank extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/KeuanganReport/KasBankMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_bank = Modules::data_bank();
        $cmb_bank = $data_bank->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank-ID" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..." required=""');

        $data_tipe_jurnal = Modules::data_tipe_jurnal();
        $cmb_tipe_jurnal = $data_tipe_jurnal->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="jt-ID" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Tipe Jurnal..."');

        $data_posted = Modules::data_posted();
        $cmb_posted = $data_posted->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="is-Posted" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Posting..."');

        return view('akuntansi_report.keuangan_report.kas_bank.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_bank',
            'cmb_tipe_jurnal',
            'cmb_posted'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'bank_id'   => get_var('bank_id'),
            'pctid'     => get_var('pctid'),
            'jtid'      => get_var('jtid'),
            'is_posted' => get_var('is_posted')
        );

        $sdate = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $edate = dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $data['status'] = 'saldo_awal';

        $rs_awal = KasBankMdl::list($data);

        $saldo_awal = $rs_awal->fields['saldo_awal'];

        $data['status'] = '';

        $rs = KasBankMdl::list($data);

        $data_bank = Modules::GetBank($data['bank_id']);

        $nama_bank = $data_bank['bank_nama'];

        return view('akuntansi_report.keuangan_report.kas_bank.cetak', compact(
            'data',
            'sdate',
            'edate',
            'saldo_awal',
            'rs',
            'nama_bank'
        ));
    } /*}}}*/
}
?>