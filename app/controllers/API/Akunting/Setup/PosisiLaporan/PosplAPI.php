<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PosplAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/PosisiLaporan/PosplMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = PosplMdl::list($data, true)->RecordCount();
        $rs = PosplMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs->fields['level']);

            $record[] = array(
                "pplid"          => $rs->fields['pplid'],
                "urutan"        => $rs->fields['urutan'],
                "kode_pos"      => $rs->fields['kode_pos'],
                "nama_pos"      => $space.$rs->fields['nama_pos'],
                "jenis_pos"     => $rs->fields['jenis_pos'],
                "parent_pos"    => $rs->fields['parent_pos'],
                "sum_total"     => $rs->fields['sum_total'],
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
        $pplid = get_var('pplid', 0);

        $rsd = PosplMdl::posisi_detail($pplid);

        if (!$rsd->EOF)
        {
            $data_pos = FieldsToObject($rsd->fields);

            $is_aktif = $data_pos->is_aktif ?? 'f';
        }
        else
        {
            $data_pos = New stdClass();

            $data_pos->pplid = $pplid;

            $data_pos->jenis_pos = '';

            $data_pos->level = 0;

            $is_aktif = 't';
        }

        $rs_jenis_pos = PosplMdl::jenis_pos();
        $cmb_jenis_pos = $rs_jenis_pos->GetMenu2('jenis_pos', $data_pos->jenis_pos, true, false, 0, 'class="form-select form-select-sm rounded-1" id="jenis_pos" data-control="select2" data-hide-search="true" data-placeholder="Pilih..." required=""');

        $rs_parent_post = PosplMdl::parent_post($data_pos->pplid);
        $cmb_parent_pos = $rs_parent_post->GetMenu2('parent_pplid', $data_pos->parent_pplid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="parent_pplid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.setup.posisi_laporan.pos_pl.form', compact(
            'data_pos',
            'cmb_jenis_pos',
            'cmb_parent_pos',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = PosplMdl::save_posisi();

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