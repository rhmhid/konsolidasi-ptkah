<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class ManualApPaymentAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/ManualApPaymentMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'suppid'    => get_var('suppid'),
            'doctor_id' => get_var('doctor_id'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = ManualApPaymentMdl::list($data, true)->RecordCount();
        $rs = ManualApPaymentMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'mapid'         => $rs->fields['mapid'],
                'paydate'       => dbtstamp2stringlong_ina($rs->fields['paydate']),
                'paycode'       => $rs->fields['paycode'],
                'no_bayar'      => $rs->fields['no_bayar'],
                'nama_supp'     => $rs->fields['nama_supp'],
                'nama_dokter'   => $rs->fields['nama_dokter'],
                'amount'        => format_uang($rs->fields['amount'], 2),
                'user_input'    => $rs->fields['useri'],
                'glid'          => $rs->fields['glid'],
                'suppid'        => $rs->fields['suppid'],
            );

            $rs->MoveNext();
        }

        $data = array(
            'draw'              => $data['draw'],
            'recordsTotal'      => $jmlbris,
            'recordsFiltered'   => $jmlbris,
            'data'              => $record
        );

        $this->response($data, REST::HTTP_OK);
    } /*}}}*/

    public function form_get () /*{{{*/
    {
        $mapid = get_var('mapid', 0);
        $AddInv = '';

        $rsd = ManualApPaymentMdl::detail_trans($mapid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->paydate = date('d-m-Y H:i', strtotime($data_head->paydate));

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $mapdid = $data_db->mapdid;
                $maid = $data_db->maid;
                $apcode = $data_db->apcode;
                $apdate = date('d-m-Y H:i', strtotime($data_db->apdate));
                $no_inv = $data_db->no_inv;
                $nominal_inv = floatval($data_db->nominal_inv);
                $nominal_hutang = floatval($data_db->nominal_hutang);
                $sisa_inv = floatval($nominal_inv) - floatval($nominal_hutang);

                $AddInv .= "AddInv ($maid, '$apcode', '$apdate', '$no_inv', '$nominal_inv', '$sisa_inv', '$nominal_hutang', $mapdid)\n";

                $rsd->MoveNext();
            }

            $AddInv .= "FormatMoney()\n";
            $AddInv .= "summaryAmount()\n";
        }
        else
        {
            $data_head = New stdClass();

            $data_head->mapid = $mapid;

            $data_head->paydate = date('d-m-Y H:i');

            $data_head->suppid = '';
        }

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('suppid', $data_head->suppid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..." required=""');

        $data_bank = Modules::data_bank();
        $cmb_bank = $data_bank->GetMenu2('bank_id', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..." required=""');

        $data_cara_bayar = Modules::data_cara_bayar();
        $cmb_cara_bayar = $data_cara_bayar->GetMenu2('cara_bayar', $data_head->cara_bayar, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="cara_bayar" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cara Bayar..." required=""');

        $data_doctor = Modules::data_doctor();
        $cmb_doctor = $data_doctor->GetMenu2('doctor_id', $data_head->doctor_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="doctor_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Dokter..."');

        return view('akunting.hutang_supplier.manual_ap_payment.form', compact(
            'data_head',
            'cmb_supp',
            'cmb_bank',
            'cmb_cara_bayar',
            'cmb_doctor',
            'AddInv'
        ));
    } /*}}}*/

    public function cari_invoice_get () /*{{{*/
    {
        $res = ManualApPaymentMdl::data_invoice();

        $dtJSON = array();
        while (!$res->EOF)
        {
            $res->fields['apdate'] = date('d-m-Y', strtotime($res->fields['apdate']));

            $res->fields['nominal_inv'] = floatval($res->fields['nominal_inv']);

            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = ManualApPaymentMdl::save_trans();

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Disimpan'
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $msg
            );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/

    public function delete_post ($myid) /*{{{*/
    {
        $msg = ManualApPaymentMdl::delete_trans($myid);

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Dihapus'
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $msg
            );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/

    public function list_inv_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'suppid'    => get_var('suppid'),
            'doctor_id' => get_var('doctor_id'),
            'no_inv'    => get_var('no_inv'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = ManualApPaymentMdl::list_inv($data, true)->RecordCount();
        $rs = ManualApPaymentMdl::list_inv($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {


            $record[] = array(
                'maid'          => $rs->fields['maid'],
                'apdate'        => dbtstamp2stringlong_ina($rs->fields['apdate']),
                'no_inv'        => $rs->fields['no_inv'],
                'apcode'        => $rs->fields['apcode'],
                'nama_supp' => $rs->fields['nama_supp'],
                'keterangan'    => $rs->fields['keterangan'],
                'nominal_inv'   => format_uang($rs->fields['nominal_inv'], 2),
                'sisa_inv'      => format_uang($rs->fields['sisa'], 2),
                'nominal_inv_noformat'   => $rs->fields['nominal_inv'],
                'sisa_inv_noformat'      => $rs->fields['sisa'],
                'user_input'    => $rs->fields['useri'],
                'glid'          => $rs->fields['glid'],
                'suppid'        => $rs->fields['suppid'],
            
            );

            $rs->MoveNext();
        }

        $data = array(
            'draw'              => $data['draw'],
            'recordsTotal'      => $jmlbris,
            'recordsFiltered'   => $jmlbris,
            'data'              => $record
        );

        $this->response($data, REST::HTTP_OK);
    } /*}}}*/


}
?>