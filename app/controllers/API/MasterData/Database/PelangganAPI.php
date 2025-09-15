<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PelangganAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Database/PelangganMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'kode_nama_cust'    => get_var('kode_nama_cust'),
            's_gcustid'         => get_var('s_gcustid'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = PelangganMdl::list($data, true)->RecordCount();
        $rs = PelangganMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "custid"        => $rs->fields['custid'],
                "custid_enc"    => encrypt($rs->fields['custid']),
                "nama_group"    => $rs->fields['nama_group'],
                "kode_customer" => $rs->fields['kode_customer'],
                "nama_customer" => $rs->fields['nama_customer'],
                "coa_ar"        => $rs->fields['coa_ar'],
                "is_aktif"      => $rs->fields['is_aktif'],
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
        $custid = get_var('custid', 0);

        $rsd = PelangganMdl::pelanggan_detail($custid);

        if (!$rsd->EOF)
        {
            $data_cust = FieldsToObject($rsd->fields);

            $is_aktif = $data_cust->is_aktif ?? 'f';
        }
        else
        {
            $data_cust = New stdClass();

            $data_cust->custid = $custid;

            $is_aktif = 't';
        }

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        $data_group = PelangganMdl::pelanggan_group();
        $cmb_group = $data_group->GetMenu2('gcustid', $data_cust->gcustid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="gcustid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_coa_ar = PelangganMdl::setup_coa_ar();
        $cmb_coa_ar = $rs_coa_ar->GetMenu2('coaid_ar', $data_cust->coaid_ar, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_ar" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');


        $data_cabang = Modules::data_cabang();
        $cmb_branch = $data_cabang->GetMenu2('bid', $data_cust->bid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        return view('master_data.database.pelanggan.form', compact(
            'data_cust',
            'cmb_group',
            'cmb_coa_ar',
            'cmb_branch',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = PelangganMdl::cek_kode($kode);

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
        $msg = PelangganMdl::save_pelanggan();

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