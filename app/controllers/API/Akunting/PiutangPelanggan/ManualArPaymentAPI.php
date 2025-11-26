<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class ManualArPaymentAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/PiutangPelanggan/ManualArPaymentMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'custid'    => get_var('custid'),
            'bank_id'   => get_var('bank_id'),
	    'pegawai_id'=> get_var('pegawai_id'),
	    'no_pay' 	=> get_var('no_pay'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = ManualArPaymentMdl::list($data, true)->RecordCount();
        $rs = ManualArPaymentMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'mapid'         => $rs->fields['mapid'],
                'paydate'       => dbtstamp2stringlong_ina($rs->fields['paydate']),
                'paycode'       => $rs->fields['paycode'],
                'no_terima'      => $rs->fields['no_terima'],
                'nama_customer'     => $rs->fields['nama_customer'],
                'nama_pegawai'     => $rs->fields['nama_pegawai'],
                'bank_nama'     => $rs->fields['bank_nama'],
                'amount'        => format_uang($rs->fields['amount'], 2),
                'user_input'    => $rs->fields['useri'],
                'glid'          => $rs->fields['glid'],
                'custid'        => $rs->fields['custid'],
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
        $AddCoa = '';

        $rsd = ManualArPaymentMdl::detail_trans($mapid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->paydate = date('d-m-Y H:i', strtotime($data_head->paydate));
            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $mapid = $data_db->mapid;
                $maid = $data_db->maid;
                $arcode = $data_db->arcode;
                $ardate = date('d-m-Y H:i', strtotime($data_db->ardate));
                $no_inv = $data_db->no_inv;
                $nominal_inv = floatval($data_db->nominal_inv);
                $nominal_piutang = floatval($data_db->nominal_piutang);
                $sisa_inv = floatval($nominal_inv) - floatval($nominal_piutang);

                $AddInv .= "AddInv ($maid, '$arcode', '$ardate', '$no_inv', '$nominal_inv', '$sisa_inv', '$nominal_piutang', $mapid)\n";

                $rsd->MoveNext();
            }

            $AddInv .= "FormatMoney()\n";
            $AddInv .= "summaryAmount()\n";

            $rsAddless = ManualArPaymentMdl::detail_trans_addless($mapid);


            while (!$rsAddless->EOF)
            {
                $data_db_addless = FieldsToObject($rsAddless->fields);

                $mapaid     = $data_db_addless->mapaid;
                $coaid      = $data_db_addless->coaid; 
                $ket_addless= $data_db_addless->ket_addless; 
                $debet      = floatval($data_db_addless->debet);
                $credit     = floatval($data_db_addless->credit);

                $AddCoa .= "AddCoa ($coaid, '$ket_addless',$debet, $credit, $mapaid)\n";

                $rsAddless->MoveNext();
            }

            $AddCoa .= "FormatMoney()\n";
            $AddCoa .= "hitungAddLess()\n";
            $AddCoa .= "subAmount()\n";

        }
        else
        {
            $data_head = New stdClass();

            $data_head->mapid = $mapid;

            $data_head->paydate = date('d-m-Y H:i');

            $data_head->suppid = '';
        }

        $data_cust = Modules::data_customer();
        $cmb_cust = $data_cust->GetMenu2('custid', $data_head->custid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="custid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Customer..." required=""');


        $data_bank = Modules::data_bank();
        $cmb_bank = $data_bank->GetMenu2('bank_id', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..." required=""');

        $data_cara_terima = Modules::data_cara_bayar();
        $cmb_cara_terima = $data_cara_terima->GetMenu2('cara_terima', $data_head->cara_terima, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="cara_terima" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cara Bayar..." required=""');


        $data_bank_ar = Modules::data_bank_cc();
        $cmb_bank_ar = $data_bank_ar->GetMenu2('bank_ar', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_ar" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        $data_karyawan = Modules::data_karyawan();
        $cmb_karyawan = $data_karyawan->GetMenu2('pegawai_id', $data_head->pegawai_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="pegawai_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Karyawan..."');



        $data_coa = Modules::data_coa_addless_ar();
        $row_coa = "::Pilih COA ...";

        while (!$data_coa->EOF)
        {
            $row = FieldsToObject($data_coa->fields);

            $row_coa .= ";".$row->coaid.":".$row->coatid.":".$row->coa;

            $data_coa->MoveNext();
        }


        return view('akunting.piutang_pelanggan.manual_ar_payment.form', compact(
            'data_head',
            'cmb_cust',
            'cmb_bank',
            'cmb_bank_ar',
            'cmb_karyawan',
            'cmb_cara_terima',
            'row_coa',
            'AddInv',
            'AddCoa'
        ));
    } /*}}}*/


    public function cari_invoice_get () /*{{{*/
    {
        $res = ManualArPaymentMdl::data_invoice();

        $dtJSON = array();
        while (!$res->EOF)
        {
            $res->fields['ardate'] = date('d-m-Y', strtotime($res->fields['ardate']));

            $res->fields['nominal_inv'] = floatval($res->fields['nominal_inv']);

            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = ManualArPaymentMdl::save_trans();

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
        $msg = ManualArPaymentMdl::delete_trans($myid);

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
            'custid'    => get_var('custid'),
            'bank_ar'   => get_var('bank_id'),
            'pegawai_id'=> get_var('pegawai_id'),
            'no_inv'    => get_var('no_inv'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = ManualArPaymentMdl::list_inv($data, true)->RecordCount();
        $rs = ManualArPaymentMdl::list_inv($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'maid'          => $rs->fields['maid'],
                'ardate'        => dbtstamp2stringlong_ina($rs->fields['ardate']),
                'no_inv'        => $rs->fields['no_inv'],
                'arcode'        => $rs->fields['arcode'],
                'nama_customer' => $rs->fields['nama_customer'],
                'nama_pegawai'  => $rs->fields['nama_pegawai'],
                'bank_nama'     => $rs->fields['bank_nama'],
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
