<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PosArusKasAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/PosisiLaporan/PosArusKasMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'start'         => get_var('start'),
            's_jenis_pos'   => get_var('s_jenis_pos'),
            'length'        => get_var('length'),
        );

        $jmlbris = PosArusKasMdl::list($data, true)->RecordCount();
        $rs = PosArusKasMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs->fields['level']);

            $record[] = array(
                "pcfid"         => $rs->fields['pcfid'],
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
        $pcfid = get_var('pcfid', 0);
        $jenis_pos = get_var('jenis_pos', 0);

        $rsd = PosArusKasMdl::posisi_detail($pcfid);

        if (!$rsd->EOF)
        {
            $data_pos = FieldsToObject($rsd->fields);

            $sum_total = $data_pos->sum_total ?? 'f';

            $is_aktif = $data_pos->is_aktif ?? 'f';
        }
        else
        {
            $data_pos = New stdClass();

            $data_pos->pcfid = $pcfid;

            $data_pos->jenis_pos = $jenis_pos;

            $data_pos->level = 0;

            $sum_total = 't';

            $is_aktif = 't';
        }

        $rs_parent_post = PosArusKasMdl::parent_post($data_pos->jenis_pos, $data_pos->pcfid);
        $cmb_parent_pos = $rs_parent_post->GetMenu2('parent_pcfid', $data_pos->parent_pcfid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="parent_pcfid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $chk_sum_total = $sum_total == 't' ? 'checked=""' : '';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.setup.posisi_laporan.pos_arus_kas.form', compact(
            'data_pos',
            'cmb_parent_pos',
            'chk_sum_total',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = PosArusKasMdl::save_posisi();

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