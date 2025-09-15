<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PettyCashAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/PettyCashMdl');
    } /*}}}*/

    public function list_type_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'coaid'         => get_var('coaid'),
            'type_trans'    => get_var('type_trans'),
            'keterangan'    => get_var('keterangan'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = PettyCashMdl::list_type($data, true)->RecordCount();
        $rs = PettyCashMdl::list_type($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'pctid'         => $rs->fields['pctid'],
                'keterangan'    => $rs->fields['keterangan'],
                'type_trans'    => $rs->fields['type_trans'],
                'coa'           => $rs->fields['coa'],
                'is_aktif'      => $rs->fields['is_aktif'],
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

    public function form_type_get () /*{{{*/
    {
        $pctid = get_var('pctid', 0);

        $rsd = PettyCashMdl::type_detail($pctid);

        if (!$rsd->EOF)
        {
            $data_db = FieldsToObject($rsd->fields);

            $type_trans = $data_db->type_trans;

            $is_aktif = $data_db->is_aktif ?? 'f';
        }
        else
        {
            $data_db = New stdClass();

            $data_db->pctid = $pctid;

            $type_trans = '';

            $is_aktif = 't';
        }

        $rs_coa = PettyCashMdl::data_coa();
        $cmb_coa = $rs_coa->GetMenu2('coaid', $data_db->coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $chk_tipe_1 = $type_trans == 1 ? '' : 'checked=""';
        $chk_tipe_2 = $type_trans == 2 ? 'checked=""' : '';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.petty_cash.form_type', compact(
            'data_db',
            'cmb_coa',
            'chk_tipe_1',
            'chk_tipe_2',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function save_type_patch () /*{{{*/
    {
        $msg = PettyCashMdl::save_type();

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

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'bank_id'       => get_var('bank_id'),
            'pccode'        => get_var('pccode'),
            'keterangan'    => get_var('keterangan'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = PettyCashMdl::list($data, true)->RecordCount();
        $rs = PettyCashMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'pcid'          => $rs->fields['pcid'],
                'pcdate'        => dbtstamp2stringina($rs->fields['pcdate']),
                'pccode'        => $rs->fields['pccode'],
                'cash_book'     => $rs->fields['cash_book'],
                'keterangan'    => $rs->fields['keterangan'],
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
        $pcid = get_var('pcid', 0);
        $AddTrans = '';

        $rsd = PettyCashMdl::detail_trans($pcid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            // $data_head->pcdate = date('d-m-Y H:i', strtotime($data_head->pcdate));
            $data_head->pcdate = date('d-m-Y', strtotime($data_head->pcdate));

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $pctid = $data_db->pctid;
                $ket_trans = $data_db->ket_trans;
                $type_trans = $data_db->type_trans;
                $coatid = $data_db->coatid;
                $notes = $data_db->notes;
                $debet = $data_db->debet;
                $credit = $data_db->credit;
                $pccid = $data_db->pccid;
                $pcdid = $data_db->pcdid;

                $AddTrans .= "AddTrans ($pctid, '$ket_trans', $type_trans, $coatid, '$notes', $debet, $credit, '".$pccid."', $pcdid)\n";

                $rsd->MoveNext();
            }

            $AddTrans .= "FormatMoney()\n";
            $AddTrans .= "summaryAmount()\n";
            $AddTrans .= "get_saldo()\n";
        }
        else
        {
            $data_head = New stdClass();

            $data_head->pcid = $pcid;

            // $data_head->pcdate = date('d-m-Y H:i');
            $data_head->pcdate = date('d-m-Y');

            $data_head->bank_id = '';
        }

        $rs_cash_book = PettyCashMdl::data_cash_book();
        $cmb_cash_book = $rs_cash_book->GetMenu2('bank_id', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $data_cost_center = Modules::data_cost_center();
        $row_cost_center = ":Pilih Cost Center ...";

        while (!$data_cost_center->EOF)
        {
            $row = FieldsToObject($data_cost_center->fields);

            $row_cost_center .= ";".$row->pccid.":".$row->pcc;

            $data_cost_center->MoveNext();
        }

        return view('akunting.petty_cash.form', compact(
            'data_head',
            'cmb_cash_book',
            'row_cost_center',
            'AddTrans'
        ));
    } /*}}}*/

    public function check_saldo_get ($mybank) /*{{{*/
    {
        $saldo = PettyCashMdl::check_saldo($mybank);

        $dtJSON = array(
            'success'   => true,
            'saldo'     => floatval($saldo)
        );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/

    public function cari_trans_get () /*{{{*/
    {
        $res = PettyCashMdl::data_trans();

        $dtJSON = array();
        while (!$res->EOF)
        {
            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = PettyCashMdl::save();

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
        $msg = PettyCashMdl::delete_trans($myid);

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
