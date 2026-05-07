<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class BukuBesar extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/BukuBesarMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $sPeriod = $ePeriod = date('d-m-Y');

        $data_posted = Modules::data_posted();
        $cmb_posted = $data_posted->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sPosted" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $data_coa = Modules::data_coa();
        $cmb_coa_from = $data_coa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCoaFrom" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $data_coa->MoveFirst();
        $cmb_coa_to = $data_coa->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sCoaTo" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        return view('akuntansi_report.buku_besar.list', compact(
            'sPeriod',
            'ePeriod',
            'cmb_posted',
            'cmb_coa_from',
            'cmb_coa_to',
        ));
    } /*}}}*/

    public function cetak () /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
            'sdate'         => get_var('sdate'),
            'edate'         => get_var('edate'),
            'is_posted'     => get_var('is_posted'),
            'coaid_from'    => get_var('coaid_from'),
            'coaid_to'      => get_var('coaid_to'),
            'with_bb'       => get_var('with_bb'),
        );

        $coa_vs = get_var('coa_vs');

        $rs = BukuBesarMdl::list($data);

        $coa_from = $rs->fields['coacode'];

        foreach ($rs as $key)
            $coa_to = $key['coacode'];

        return view('akuntansi_report.buku_besar.cetak', compact(
            'data',
            'coa_vs',
            'rs',
            'coa_from',
            'coa_to'
        ));
    } /*}}}*/
}
?>