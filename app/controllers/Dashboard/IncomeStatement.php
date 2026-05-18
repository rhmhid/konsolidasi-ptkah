<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class IncomeStatement extends BaseController
{
    static $ho_jkk, $ho_kah;

    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data = array(
            'bid'   => get_var('bid'),
            'month' => get_var('month', date('n')),
            'year'  => get_var('year', date('Y')),
        );

        $data_cabang = Modules::data_cabang_all();
        $cmb_cabang = $data_cabang->GetMenu2('', $data['bid'], true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="s-Bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cabang..."');

        return view('dashboard.income_statement', compact(
            'cmb_cabang'
        ));
    } /*}}}*/
}
?>