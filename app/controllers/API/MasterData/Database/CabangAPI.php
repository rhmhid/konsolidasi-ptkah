<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class CabangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Database/CabangMdl');
    } /*}}}*/

    public function cek_kode_post ($type, $kode) /*{{{*/
    {
        $res = CabangMdl::cek_kode($type, $kode);

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

    public function list_get ($type) /*{{{*/
    {
        if ($type == 'tipe') self::cabang_tipe();
        elseif ($type == 'wilayah') self::cabang_wilayah();
    } /*}}}*/

    static function cabang_tipe () /*{{{*/
    {
        return view('master_data.database.cabang.tipe');
    } /*}}}*/

    static function cabang_wilayah () /*{{{*/
    {
        return view('master_data.database.cabang.wilayah');
    } /*}}}*/

    public function form_get ($type) /*{{{*/
    {
        $rsd = CabangMdl::detail_cabang($type);

        if (!$rsd->EOF)
        {
            $data_rs = FieldsToObject($rsd->fields);

            $is_primary = $data_rs->is_primary ?? 'f';

            $is_aktif = $data_rs->is_aktif ?? 'f';
        }
        else
        {
            $data_rs = New stdClass();

            $is_primary = '';

            $is_aktif = 't';
        }

        $rs_tipe = CabangMdl::branch_tipe();
        $cmb_tipe = $rs_tipe->GetMenu2('btid', $data_rs->btid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="btid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Tipe" required=""');

        $rs_wilayah = CabangMdl::branch_wilayah();
        $cmb_wilayah = $rs_wilayah->GetMenu2('bwid', $data_rs->bwid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bwid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Wilayah" required=""');

        $data_coa = Modules::data_coa();
        $cmb_coa = $data_coa->GetMenu2('coaid_branch', $data_rs->coaid_branch, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_branch" data-control="select2" data-allow-clear="true" data-placeholder="Pilih C.O.A"');

        $chk_primary = $is_primary == 't' ? 'checked=""' : '';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('master_data.database.cabang.form', compact(
            'data_rs',
            'cmb_tipe',
            'cmb_wilayah',
            'cmb_coa',
            'chk_primary',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function list_data_get ($type) /*{{{*/
    {
        if ($type == 'tipe') $data = self::data_cabang_tipe();
        elseif ($type == 'wilayah') $data = self::data_cabang_wilayah();
        else $data = self::data_cabang();

        $this->response($data, REST::HTTP_OK);
    } /*}}}*/

    static function data_cabang_tipe () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = CabangMdl::list_cabang_tipe($data, true)->RecordCount();
        $rs = CabangMdl::list_cabang_tipe($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'btid'          => $rs->fields['btid'],
                'kode_tipe'     => $rs->fields['kode_tipe'],
                'nama_tipe'     => $rs->fields['nama_tipe'],
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

        return $data;
    } /*}}}*/

    static function data_cabang_wilayah () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = CabangMdl::list_cabang_wilayah($data, true)->RecordCount();
        $rs = CabangMdl::list_cabang_wilayah($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'bwid'          => $rs->fields['bwid'],
                'kode_wilayah'  => $rs->fields['kode_wilayah'],
                'nama_wilayah'  => $rs->fields['nama_wilayah'],
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

        return $data;
    } /*}}}*/

    static function data_cabang () /*{{{*/
    {
        $data = array(
            'draw'                  => get_var('draw'),
            's_btid'                => get_var('s_btid'),
            's_bwid'                => get_var('s_bwid'),
            's_kode_nama_branch'    => get_var('s_kode_nama_branch'),
            'start'                 => get_var('start'),
            'length'                => get_var('length'),
        );

        $jmlbris = CabangMdl::list_cabang($data, true)->RecordCount();
        $rs = CabangMdl::list_cabang($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'bid'               => $rs->fields['bid'],
                'bwid'              => $rs->fields['bwid'],
                'wilayah'           => $rs->fields['wilayah'],
                'branch_code'       => $rs->fields['branch_code'],
                'branch_name'       => $rs->fields['branch_name'],
                'branch_sub_corp'   => $rs->fields['branch_sub_corp'],
                'tipe'              => $rs->fields['tipe'],
                'branch_addr'       => $rs->fields['branch_addr'],
                'branch_desc'       => $rs->fields['branch_desc'],
                'is_primary'        => $rs->fields['is_primary'],
                'is_aktif'          => $rs->fields['is_aktif'],
                'status_txt'        => get_status_aktif($rs->fields['is_aktif']),
                'status_css'        => get_status_aktif($rs->fields['is_aktif'], 'css'),
                'status_icon'       => get_status_aktif($rs->fields['is_aktif'], 'icon'),
            );

            $rs->MoveNext();
        }

        $data = array(
            'draw'              => $data['draw'],
            'recordsTotal'      => $jmlbris,
            'recordsFiltered'   => $jmlbris,
            'data'              => $record
        );

        return $data;
    } /*}}}*/

    public function save_patch ($type) /*{{{*/
    {
        if ($type == 'tipe') $msg = CabangMdl::save_cabang_tipe();
        elseif ($type == 'wilayah') $msg = CabangMdl::save_cabang_wilayah();
        else $msg = CabangMdl::save_cabang();

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

    public function assign_branch_get () /*{{{*/
    {
        $data = array(
            'item_type' => get_var('item_type'),
            'base_id'   => get_var('base_id', '', 't')
        );

        $rs = CabangMdl::cabang_by_assign($data);

        if ($rs == '') $availableBranch = []; 
        else $availableBranch = explodeData(',', $rs); 

        $rs_cabang = CabangMdl::list_cabang($data, true);

        return view('master_data.database.cabang.assign_branch', compact(
            'data',
            'availableBranch',
            'rs_cabang'
        ));
    } /*}}}*/

    public function save_assign_branch_patch () /*{{{*/
    {
        $msg = CabangMdl::save_assign_branch();

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