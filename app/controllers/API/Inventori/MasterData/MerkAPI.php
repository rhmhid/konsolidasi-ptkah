<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class MerkAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/MerkMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_kode_nama_merk'  => get_var('s_kode_nama_merk'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = MerkMdl::list($data, true)->RecordCount();
        $rs = MerkMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'mmid'          => $rs->fields['mmid'],
                'kode_merk'     => $rs->fields['kode_merk'],
                'nama_merk'     => $rs->fields['nama_merk'],
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
        $mmid = get_var('mmid', 0);

        $rsd = MerkMdl::mek_detail($mmid);

        if (!$rsd->EOF)
        {
            $data_merk = FieldsToObject($rsd->fields);

            $is_aktif = $data_merk->is_aktif ?? 'f';
        }
        else
        {
            $data_merk = New stdClass();

            $data_merk->mmid = $mmid;

            $is_aktif = 't';
        }

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('inventori.masterdata.merk.form', compact(
            'data_merk',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = MerkMdl::cek_kode($kode);

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
        $msg = MerkMdl::save_merk();

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