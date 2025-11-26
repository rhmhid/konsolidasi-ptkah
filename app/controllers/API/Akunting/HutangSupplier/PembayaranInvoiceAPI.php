<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PembayaranInvoiceAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/PembayaranInvoiceMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'suppid'    => get_var('suppid'),
            'paycode'   => get_var('paycode'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = PembayaranInvoiceMdl::list($data, true)->RecordCount();
        $rs = PembayaranInvoiceMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'appid'         => $rs->fields['appid'],
                'paydate'       => dbtstamp2stringlong_ina($rs->fields['paydate']),
                'paycode'       => $rs->fields['paycode'],
                'nama_supp'     => $rs->fields['nama_supp'],
                'totpay'        => format_uang($rs->fields['totpay'], 2),
                'user_input'    => $rs->fields['useri'],
                'glid'          => $rs->fields['glid'],
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
        $appid = get_var('appid', 0);
        $AddPay = '';

        $rsd = PembayaranInvoiceMdl::detail_trans($appid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->paydate = date('d-m-Y H:i', strtotime($data_head->paydate));

            $data_head->tgl_bayar = date('d-m-Y', strtotime($data_head->tgl_bayar));

            $data_head->potongan = floatval($data_head->potongan);

            $data_head->pembulatan = floatval($data_head->pembulatan);

            $data_head->other_cost = floatval($data_head->other_cost);

            $AddPay .= "$('.info-bank-supp').html('".$data_head->bank."')\n$('.info-norek-supp').html('".$data_head->no_rek."')\n";

            $totInv = 0;

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $appdid = $data_db->appdid;
                $apsid = $data_db->apsid;
                $apcode = $data_db->apcode;
                $apdate = date('d-m-Y', strtotime($data_db->apdate));
                $no_invoice = $data_db->no_invoice;
                $duedate = date('d-m-Y', strtotime($data_db->duedate));
                $no_faktur_pajak = $data_db->no_faktur_pajak;
                $ket_ap = $data_db->ket_ap;
                $nominal_hutang = floatval($data_db->nominal_hutang);
                $nominal_payment = floatval($data_db->nominal_payment);
                $sisa_hutang = floatval($nominal_hutang - $nominal_payment);

                $AddPay .= "AddPay('$apsid', '$apcode', '$apdate', '$no_invoice', '$duedate', '$no_faktur_pajak', '$ket_ap', '$nominal_hutang', '$sisa_hutang', '$nominal_payment', '$appdid')\n";

                $totInv += $nominal_payment;

                $rsd->MoveNext();
            }

            $rs_addless = PembayaranInvoiceMdl::detail_addless($appid);

            while (!$rs_addless->EOF)
            {
                $data_al = FieldsToObject($rs_addless->fields);

                $appaid = $data_al->appaid;
                $coaid = $data_al->coaid;
                $ket_addless = $data_al->ket_addless;
                $debet = floatval($data_al->debet);
                $credit = floatval($data_al->credit);
                $pccid = $data_al->pccid;

                $AddPay .= "AddCoa('$coaid', '$ket_addless', '$debet', '$credit', '$pccid', '$appaid')\n";

                $rs_addless->MoveNext();
            }

            $AddPay .= "\nFormatMoney()\n";

            $AddPay .= "\$('#vtotal-inv').html(MoneyFormat(".$totInv."))\n";

            $AddPay .= "\ncalcAddless()\n";
        }
        else
        {
            $data_head = New stdClass();

            $data_head->appid = $appid;

            $data_head->paydate = date('d-m-Y H:i');

            $data_head->tgl_bayar = date('d-m-Y');

            $data_head->potongan = 0;

            $data_head->pembulatan = 0;

            $data_head->other_cost = 0;
        }

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('suppid', $data_head->suppid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..." required=""');

        $data_bank = Modules::data_bank();
        $cmb_bank = $data_bank->GetMenu2('bank_id', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..." required=""');

        $data_cara_bayar = Modules::data_cara_bayar();
        $cmb_cara_bayar = $data_cara_bayar->GetMenu2('cara_bayar', $data_head->cara_bayar, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="cara_bayar" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cara Bayar..." required=""');

        $data_coa = PembayaranInvoiceMdl::data_coa();

        $row_coa = "::Pilih COA ...";

        while (!$data_coa->EOF)
        {
            $row = FieldsToObject($data_coa->fields);

            $row_coa .= ";".$row->coaid.":".$row->coatid.":".$row->coa;

            $data_coa->MoveNext();
        }

        $data_cost_center = Modules::data_cost_center();

        $row_cost_center = ":Pilih Cost Center ...";

        while (!$data_cost_center->EOF)
        {
            $row = FieldsToObject($data_cost_center->fields);

            $row_cost_center .= ";".$row->pccid.":".$row->pcc;

            $data_cost_center->MoveNext();
        }

        return view('akunting.hutang_supplier.pembayaran_invoice.form', compact(
            'data_head',
            'cmb_supp',
            'cmb_bank',
            'cmb_cara_bayar',
            'row_coa',
            'row_cost_center',
            'AddPay'
        ));
    } /*}}}*/

    public function info_supplier_get ($myid) /*{{{*/
    {
        $data_supp = Modules::GetSupplier($myid);

        $dtJSON = array(
            'bank'      => $data_supp['bank'],
            'no_rek'    => $data_supp['no_rek'],
        );

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function list_outstanding_ap_get ($myid) /*{{{*/
    {
        $res = PembayaranInvoiceMdl::list_outstanding_ap($myid);

        $dtJSON = array();
        while (!$res->EOF)
        {
            $res->fields['apdate'] = date('d-m-Y', strtotime($res->fields['apdate']));

            $res->fields['duedate'] = date('d-m-Y', strtotime($res->fields['duedate']));

            $res->fields['nominal_hutang'] = floatval($res->fields['nominal_hutang']);

            $res->fields['sisa_hutang'] = floatval($res->fields['sisa_hutang']);

            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = PembayaranInvoiceMdl::save_trans();

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
        $msg = PembayaranInvoiceMdl::delete_trans($myid);

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
}
?>