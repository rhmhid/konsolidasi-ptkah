<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class SupplierAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/SupplierMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_type_supp'       => get_var('s_type_supp'),
            'kode_nama_supp'    => get_var('kode_nama_supp'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = SupplierMdl::list($data, true)->RecordCount();
        $rs = SupplierMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "suppidenc"     => encrypt($rs->fields['suppid']),
                'suppid'        => $rs->fields['suppid'],
                'kode_supp'     => $rs->fields['kode_supp'],
                'nama_supp'     => $rs->fields['nama_supp'],
                'addr_supp'     => $rs->fields['addr_supp'],
                'email_supp'    => $rs->fields['email_supp'],
                'coa_ap'        => $rs->fields['coa_ap'],
                'type_supp'     => $rs->fields['type_supp'] == 1 ? 'Non Medis' : 'Medis',
                'is_aktif'      => $rs->fields['is_aktif'],
                'status_txt'    => get_status_aktif($rs->fields['is_aktif']),
                'status_css'    => get_status_aktif($rs->fields['is_aktif'], 'css'),
                'status_icon'   => get_status_aktif($rs->fields['is_aktif'], 'icon'),
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
        $suppid = get_var('suppid', 0);

        $rs = SupplierMdl::supplier_detail($suppid);

        if (!$rs->EOF)
        {
            $data_supp = FieldsToObject($rs->fields);

            $type_supp = $data_supp->type_supp ?? 1;

            $is_aktif = $data_supp->is_aktif ?? 'f';
        }
        else
        {
            $data_supp = New stdClass();

            $data_supp->suppid = $suppid;

            $type_supp = '';

            $is_aktif = 't';
        }

        $chk_non_medis = $type_supp == 1 ? 'checked=""' : '';
        $chk_medis = $type_supp == 2 ? 'checked=""' : '';

        $rs_coa_ap = SupplierMdl::setup_coa_ap();
        $cmb_coa_ap = $rs_coa_ap->GetMenu2('coaid_ap', $data_supp->coaid_ap, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_ap" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('inventori.masterdata.supplier.form', compact(
            'data_supp',
            'chk_non_medis',
            'chk_medis',
            'cmb_coa_ap',
            'chk_aktif',
            'txt_aktif',
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = SupplierMdl::cek_kode($kode);

        $dtJSON = array();
        if ($res == '')
            $dtJSON = array(
                'success'   => true,
                'message'   => '',
                'kode'      => $kode
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $res,
                'kode'      => $kode
            );

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = SupplierMdl::save_supplier();

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
}
?>
