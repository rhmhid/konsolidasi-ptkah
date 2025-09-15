<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class DpSupplierAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/DpSupplierMdl');
    } /*}}}*/

    // public function list_get () /*{{{*/
    // {
    //     $data = array(
    //         'draw'              => get_var('draw'),
    //         'mutasi_speriod'    => get_var('mutasi_speriod', date('d-m-Y')),
    //         'mutasi_eperiod'    => get_var('mutasi_eperiod', date('d-m-Y')),
    //         'bank_from'         => get_var('bank_from'),
    //         'bank_to'           => get_var('bank_to'),
    //         'start'             => get_var('start'),
    //         'length'            => get_var('length'),
    //     );

    //     $jmlbris = DpSupplierMdl::list($data, true)->RecordCount();
    //     $rs = DpSupplierMdl::list($data, false, $data['start'], $data['length']);

    //     while (!$rs->EOF)
    //     {
    //         $record[] = array(
    //             'msid'          => $rs->fields['msid'],
    //             'mutasi_date'   => dbtstamp2stringlong_ina($rs->fields['mutasi_date']),
    //             'mutasi_code'   => $rs->fields['mutasi_code'],
    //             'bank_from'     => $rs->fields['bank_from'],
    //             'bank_to'       => $rs->fields['bank_to'],
    //             'nominal'       => format_uang($rs->fields['amount'], 2),
    //             'keterangan'    => $rs->fields['keterangan'],
    //             'user_input'    => $rs->fields['useri'],
    //         );

    //         $rs->MoveNext();
    //     }

    //     $data = array(
    //         'draw'              => $data['draw'],
    //         'recordsTotal'      => $jmlbris,
    //         'recordsFiltered'   => $jmlbris,
    //         'data'              => $record
    //     );

    //     $this->response($data, REST::HTTP_OK);
    // } /*}}}*/

    public function form_get () /*{{{*/
    {
        $mutasi_date = date('d-m-Y H:i');

        // $data_bank = Modules::data_bank();
        // $cmb_bank_from = $data_bank->GetMenu2('bank_from', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 bank-select" id="bank_from" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..." required="" data-type="from"');

        // $data_bank->MoveFirst();
        // $cmb_bank_to = $data_bank->GetMenu2('bank_to', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 bank-select" id="bank_to" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..." required="" data-type="to"');

        return view('akunting.hutang_supplier.dp_supplier.form', compact(
            '',
            // 'cmb_bank_from',
            // 'cmb_bank_to'
        ));
    } /*}}}*/

    // public function cek_saldo_post ($mybank) /*{{{*/
    // {
    //     $res = floatval(DpSupplierMdl::cek_saldo($mybank));

    //     $this->response($res, REST::HTTP_OK);
    // } /*}}}*/

    // public function save_patch () /*{{{*/
    // {
    //     $msg = DpSupplierMdl::save_trans();

    //     $dtJSON = array();
    //     if ($msg == 'true')
    //         $dtJSON = array(
    //             'success'   => true,
    //             'message'   => 'Data Berhasil Disimpan'
    //         );
    //     else
    //         $dtJSON = array(
    //             'success'   => false,
    //             'message'   => $msg
    //         );

    //     $this->response($dtJSON, REST::HTTP_CREATED);
    // } /*}}}*/

    // public function delete_post ($myid) /*{{{*/
    // {
    //     $msg = DpSupplierMdl::delete_trans($myid);

    //     $dtJSON = array();
    //     if ($msg == 'true')
    //         $dtJSON = array(
    //             'success'   => true,
    //             'message'   => 'Data Berhasil Dihapus'
    //         );
    //     else
    //         $dtJSON = array(
    //             'success'   => false,
    //             'message'   => $msg
    //         );

    //     $this->response($dtJSON, REST::HTTP_CREATED);
    // } /*}}}*/
}
?>