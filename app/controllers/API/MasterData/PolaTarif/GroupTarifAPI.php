<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class GroupTarifAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/PolaTarif/GroupTarifMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            's_kode_nama'   => get_var('s_kode_nama'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = GroupTarifMdl::list($data, true)->RecordCount();
        $rs = GroupTarifMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "gtid"          => $rs->fields['gtid'],
                "group_code"    => $rs->fields['group_code'],
                "group_name"    => $rs->fields['group_name'],
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
        $gtid = get_var('gtid', 0);

        $rsd = GroupTarifMdl::group_tarif_detail($gtid);

        if (!$rsd->EOF)
        {
            $data_group = FieldsToObject($rsd->fields);

            $is_aktif = $data_group->is_aktif ?? 'f';
        }
        else
        {
            $data_group = New stdClass();

            $data_group->gtid = $gtid;

            $is_aktif = 't';
        }

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('master_data.pola_tarif.group_tarif.form', compact(
            'data_group',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = GroupTarifMdl::cek_kode($kode);

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
        $msg = GroupTarifMdl::save_group_tarif();

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