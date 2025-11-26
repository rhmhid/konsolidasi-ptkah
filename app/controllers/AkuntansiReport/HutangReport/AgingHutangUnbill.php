<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class AgingHutangUnbill extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/HutangReport/AgingHutangUnbillMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sdate = date('d-m-Y');

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..."');

        return view('akuntansi_report/hutang_report/aging_hutang_unbill.list', compact(
            'sdate',
            'cmb_supp'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'suppid'    => get_var('suppid')
        );

        $sdate = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $rs = AgingHutangUnbillMdl::list($data);

        return view('akuntansi_report/hutang_report/aging_hutang_unbill.cetak', compact(
            'data',
            'sdate',
            'rs',
        ));
    } /*}}}*/
}
?>