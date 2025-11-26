<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class GudangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/GudangMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'                  => get_var('draw'),
            's_kode_nama_gudang'    => get_var('s_kode_nama_gudang'),
            's_lokasi'              => get_var('s_lokasi'),
            's_jenis_gudang'        => get_var('s_jenis_gudang'),
            'start'                 => get_var('start'),
            'length'                => get_var('length'),
        );

        $jmlbris = GudangMdl::list($data, true)->RecordCount();
        $rs = GudangMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'gid'               => $rs->fields['gid'],
                'kode_gudang'       => $rs->fields['kode_gudang'],
                'nama_gudang'       => $rs->fields['nama_gudang'],
                'lokasi'            => $rs->fields['lokasi'],
                'is_gudang_besar'   => $rs->fields['is_gudang_besar'],
                'is_sales'          => $rs->fields['is_sales'],
                'is_depo'           => $rs->fields['is_depo'],
                'cost_center'       => $rs->fields['cost_center'],
                'is_aktif'          => $rs->fields['is_aktif'],
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
        $gid = get_var('gid', 0);

        $rsd = GudangMdl::gudang_detail($gid);

        if (!$rsd->EOF)
        {
            $data_gudang = FieldsToObject($rsd->fields);

            $is_aktif = $data_gudang->is_aktif ?? 'f';

            $is_sales = $data_gudang->is_sales ?? 'f';

            $is_gudang = $data_gudang->is_gudang_besar ?? 'f';

            $is_depo = $data_gudang->is_depo ?? 'f';
        }
        else
        {
            $data_gudang = New stdClass();

            $data_gudang->gid = $gid;

            $data_gudang->pccid = '';

            $is_aktif = 't';

            $is_sales = '';

            $is_gudang = '';

            $is_depo = '';
        }

        $rs_cost_center = Modules::data_cost_center();
        $cmb_cost_center = $rs_cost_center->GetMenu2('pccid', $data_gudang->pccid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-md" id="pccid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        $chk_sales = $is_sales == 't' ? 'checked=""' : '';
        $chk_gudang = $is_gudang == 't' ? 'checked=""' : '';
        $chk_depo = $is_depo == 't' ? 'checked=""' : '';

        return view('inventori.masterdata.gudang.form', compact(
            'data_gudang',
            'cmb_cost_center',
            'chk_aktif',
            'txt_aktif',
            'chk_sales',
            'chk_gudang',
            'chk_depo'
        ));
    } /*}}}*/

    public function cek_kode_post () /*{{{*/
    {
        $res = GudangMdl::cek_kode();

        $dtJSON = array();
        if ($res == '')
            $dtJSON = array(
                'success'   => true,
                'message'   => ''
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $res
            );

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = GudangMdl::save_gudang();

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