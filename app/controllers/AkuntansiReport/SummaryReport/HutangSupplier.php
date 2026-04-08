<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class HutangSupplier extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        // $this->load->model('AkuntansiReport/SummaryReport/HutangSupplierMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $data_coa_hutang = Modules::data_cabang_all();
        $cmb_coa_hutang = $data_coa_hutang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-CoaHutang" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('akuntansi_report.summary_report.hutang_supplier.list', compact(
            'cmb_cabang',
            'cmb_coa_hutang'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        return view('akuntansi_report.summary_report.hutang_supplier.cetak', compact(
            'cabang',
            'data',
            'report_month',
            'data_cabang',
            'data_tb',
            'data_sum'
        ));
    } /*}}}*/
}
?>