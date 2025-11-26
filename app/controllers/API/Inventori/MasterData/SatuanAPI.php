<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class SatuanAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/SatuanMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_kode_nama_sat'   => get_var('s_kode_nama_sat'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = SatuanMdl::list($data, true)->RecordCount();
        $rs = SatuanMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'msid'          => $rs->fields['msid'],
                'kode_satuan'   => $rs->fields['kode_satuan'],
                'nama_satuan'   => $rs->fields['nama_satuan'],
                'keterangan'    => $rs->fields['keterangan'],
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

    public function form_get () /*{{{*/
    {
        $msid = get_var('msid', 0);

        $rsd = SatuanMdl::satuan_detail($msid);

        if (!$rsd->EOF)
        {
            $data_sat = FieldsToObject($rsd->fields);

            $is_aktif = $data_sat->is_aktif ?? 'f';
        }
        else
        {
            $data_sat = New stdClass();

            $data_sat->msid = $msid;

            $is_aktif = 't';
        }

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('inventori.masterdata.satuan.form', compact(
            'data_sat',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = SatuanMdl::cek_kode($kode);

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
        $msg = SatuanMdl::save_satuan();

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