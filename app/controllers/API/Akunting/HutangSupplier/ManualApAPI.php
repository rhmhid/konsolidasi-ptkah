<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class ManualApAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/HutangSupplier/ManualApMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'suppid'    => get_var('suppid'),
            'no_inv'    => get_var('no_inv'),
            'doctor_id' => get_var('doctor_id'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = ManualApMdl::list($data, true)->RecordCount();
        $rs = ManualApMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'maid'          => $rs->fields['maid'],
                'apdate'        => dbtstamp2stringlong_ina($rs->fields['apdate']),
                'no_inv'        => $rs->fields['no_inv'],
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
        $maid = get_var('maid', 0);
        $AddCoa = '';

        $rsd = ManualApMdl::detail_trans($maid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->apdate = date('d-m-Y H:i', strtotime($data_head->apdate));

            $data_head->duedate = date('d-m-Y', strtotime($data_head->duedate));

            $data_head->tgl_faktur_pajak = date('d-m-Y', strtotime($data_head->tgl_faktur_pajak));

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $coaid = $data_db->coaid;
                $amount = floatval($data_db->amount);
                $detailnote = $data_db->detailnote;
                $pccid = $data_db->pccid;
                $AddCoa .= "AddCoa ($coaid, '$detailnote', '$amount', '$madid', '$pccid')\n";

//                $AddCoa .= "AddCoa ($coaid, '$detailnote', '$amount')\n";

                $rsd->MoveNext();
            }

            $AddCoa .= "FormatMoney()\n";
            $AddCoa .= "summaryAmount()\n";
        }
        else
        {
            $data_head = New stdClass();

            $data_head->maid = $maid;

            $data_head->apdate = date('d-m-Y H:i');

            $data_head->duedate = $data_head->tgl_faktur_pajak = date('d-m-Y');

            $data_head->suppid = '';
        }

        $data_supplier = Modules::data_supplier();
        $cmb_supp = $data_supplier->GetMenu2('suppid', $data_head->suppid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier..." required=""');

        $data_doctor = Modules::data_doctor();
        $cmb_doctor = $data_doctor->GetMenu2('doctor_id', $data_head->doctor_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="doctor_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Dokter..."');

        $data_coa = ManualApMdl::data_coa();

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


        return view('akunting.hutang_supplier.manual_ap.form', compact(
            'data_head',
            'cmb_supp',
            'cmb_doctor',
            'row_coa',
            'row_cost_center',
            'AddCoa'
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = ManualApMdl::save_trans();

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
        $msg = ManualApMdl::delete_trans($myid);

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
