<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class InvoicePembelianAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/InvoicePembelianMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'sdate'             => get_var('sdate', date('d-m-Y')),
            'edate'             => get_var('edate', date('d-m-Y')),
            'suppid'            => get_var('suppid'),
            'no_inv'            => get_var('no_inv'),
            'no_faktur_pajak'   => get_var('no_faktur_pajak'),
            'apcode'            => get_var('apcode'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = InvoicePembelianMdl::list($data, true)->RecordCount();
        $rs = InvoicePembelianMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'apsid'         => $rs->fields['apsid'],
                'apdate'        => dbtstamp2stringlong_ina($rs->fields['apdate']),
                'no_inv'        => $rs->fields['no_invoice'],
                'apcode'        => $rs->fields['apcode'],
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
        $apsid = get_var('apsid', 0);
        $AddGrn = '';

        $rsd = InvoicePembelianMdl::detail_trans($apsid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->apdate = date('d-m-Y H:i', strtotime($data_head->apdate));

            $data_head->duedate = date('d-m-Y', strtotime($data_head->duedate));

            $tgl_faktur = date('d-m-Y', strtotime($data_head->tgl_faktur_pajak));

            $no_faktur_pajak = $data_head->no_faktur_pajak;

            $diskon = floatval($data_head->diskon);

            $ongkir = floatval($data_head->ongkir);

            $materai = floatval($data_head->materai);

            $ppn_persen = floatval($data_head->ppn);

            $ppn_rp = floatval($data_head->ppn_rp);

            $other_cost = floatval($data_head->other_cost);

            $total_ap = floatval($data_head->amount);

            $no_inv = $data_head->no_invoice;

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $apsdid = $data_db->apsdid;
                $grid = $data_db->grid;
                $poid = $data_db->poid;
                $pocode = $data_db->pocode;
                $grcode = $data_db->grcode;
                $total_grn = floatval($data_db->nominal);

                $AddGrn .= "AddGrn('$grid', '$poid', '$pocode', '$grcode', '$tgl_faktur', '$no_faktur_pajak', '$total_grn', '$diskon', '$ongkir', '$materai', '$ppn_persen', '$ppn_rp', '$other_cost', '$total_ap', '$no_inv', '$apsdid')\n";

                $rsd->MoveNext();
            }

            $AddGrn .= "\nFormatMoney()\n";

            $AddGrn .= "\ncalcAmunt()\n";
        }
        else
        {
            $data_head = New stdClass();

            $data_head->apsid = $apsid;

            $data_head->apdate = date('d-m-Y H:i');

            $data_head->duedate = date('d-m-Y');

            $data_head->suppid = '';

            $data_head->is_kwitansi = '';

            $data_head->is_faktur_pajak = '';

            $data_head->is_surat_jalan = '';

            $data_head->is_po = '';

            $data_head->is_terima_barang = '';

            $data_head->is_nota_retur = '';

            $data_head->is_berita_acara = '';
        }

        $chk_kwitansi = $data_head->is_kwitansi == 't' ? 'checked=""' : "";

        $chk_faktur_pajak = $data_head->is_faktur_pajak == 't' ? 'checked=""' : "";

        $chk_surat_jalan = $data_head->is_surat_jalan == 't' ? 'checked=""' : "";

        $chk_po = $data_head->is_po == 't' ? 'checked=""' : "";

        $chk_terima_barang = $data_head->is_terima_barang == 't' ? 'checked=""' : "";

        $chk_nota_retur = $data_head->is_nota_retur == 't' ? 'checked=""' : "";

        $chk_berita_acara = $data_head->is_berita_acara == 't' ? 'checked=""' : "";

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('suppid', $data_head->suppid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..." required=""');

        return view('akunting.hutang_supplier.invoice_pembelian.form', compact(
            'data_head',
            'cmb_supp',
            'chk_kwitansi',
            'chk_faktur_pajak',
            'chk_surat_jalan',
            'chk_po',
            'chk_terima_barang',
            'chk_nota_retur',
            'chk_berita_acara',
            'AddGrn'
        ));
    } /*}}}*/

    public function popup_penerimaan_get () /*{{{*/
    {
        $suppid = get_var('suppid');
        $nama_supp = Modules::get_nama_supplier($suppid);

        return view('akunting.hutang_supplier.invoice_pembelian.popup_penerimaan', compact(
		'suppid',
		'nama_supp'
        ));
    } /*}}}*/

    public function list_penerimaan_get () /*{{{*/
    {
        $res = InvoicePembelianMdl::list_penerimaan();

        $dtJSON = array();
        while (!$res->EOF)
        {
            $res->fields['grdate_txt'] = dbtstamp2stringlong_ina($res->fields['grdate']);

            $res->fields['tgl_faktur'] = date('d-m-Y', strtotime($res->fields['tgl_faktur']));

            $res->fields['subtotal'] = floatval($res->fields['subtotal']);

            $res->fields['subtotal_txt'] = format_uang($res->fields['subtotal'], 2);

            $res->fields['diskon'] = floatval($res->fields['diskon']);

            $res->fields['ongkir'] = floatval($res->fields['ongkir']);

            $res->fields['materai'] = floatval($res->fields['materai']);

            $res->fields['other_cost'] = floatval($res->fields['other_cost']);

            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = InvoicePembelianMdl::save_trans();

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
        $msg = InvoicePembelianMdl::delete_trans($myid);

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
