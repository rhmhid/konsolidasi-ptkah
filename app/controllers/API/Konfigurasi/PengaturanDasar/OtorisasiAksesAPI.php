<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class OtorisasiAksesAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model(array('Konfigurasi/PengaturanDasar/OtorisasiAksesMdl'));
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            's_otogid'      => get_var('s_otogid'),
            's_nama_user'   => get_var('s_nama_user'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = OtorisasiAksesMdl::list($data, true)->RecordCount();
        $rs = OtorisasiAksesMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "otoid"     => $rs->fields['otoid'],
                "os_group"  => $rs->fields['description'],
                "os_user"   => $rs->fields['nama_lengkap'],
                'user'      => $rs->fields['useri'],
                'waktu'     => dbtstamp2stringlong_ina($rs->fields['create_time']),
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

    public function create_get () /*{{{*/
    {
        $rs_group = Modules::data_group_otorisasi();
        $cmb_group = $rs_group->GetMenu2('otogid_val', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="otogid_val" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Group Otorisasi"');

        return view('konfigurasi.pengaturan_dasar.otorisasi_akses.create', compact('cmb_group'));
    } /*}}}*/

    public function cari_user_get () /*{{{*/
    {
        $res = OtorisasiAksesMdl::cari_user();

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
        $msg = OtorisasiAksesMdl::save_otorisasi();

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

    public function delete_patch () /*{{{*/
    {
        $msg = OtorisasiAksesMdl::delete_otorisasi();

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