<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class ControlPanelAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Konfigurasi/PengaturanDasar/ControlPanelMdl');
    } /*}}}*/

    public function list_data_configs_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            's_cgid'        => get_var('s_cgid'),
            's_deskripsi'   => get_var('s_deskripsi'),
            's_data'        => get_var('s_data'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = ControlPanelMdl::list($data, true)->RecordCount();
        $rs = ControlPanelMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $modify_time = $rs->fields['modify_time'] == '' ? '' : dbtstamp2stringlong_ina($rs->fields['modify_time']);

            $record[] = array(
                "cid"           => encrypt($rs->fields['cid']),
                "cg_urutan"     => $rs->fields['urutan'],
                "cg_name"       => $rs->fields['config_name'],
                "confname"      => $rs->fields['confname'],
                "description"   => $rs->fields['keterangan'],
                "data"          => $rs->fields['data'],
                "is_editable"   => $rs->fields['is_editable'],
                'data_type'     => $rs->fields['data_type'],
                'user'          => $rs->fields['user'],
                'waktu'         => $modify_time,
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

    public function edit_configs_get () /*{{{*/
    {
        $cid = get_var('cid', 0, 't');

        $rs = ControlPanelMdl::configs_detail($cid);

        $data_configs = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        return view('konfigurasi.pengaturan_dasar.control_panel.edit', compact('data_configs'));
    } /*}}}*/

    public function save_configs_patch () /*{{{*/
    {
        $msg = ControlPanelMdl::save_configs();

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Dirubah'
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