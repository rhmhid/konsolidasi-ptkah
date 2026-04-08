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

        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        return view('akuntansi_report/hutang_report/aging_hutang_unbill.list', compact(
            'sdate',
            'cmb_cabang'
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'bid'       => get_var('bid')
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