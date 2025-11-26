<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class HakAksesAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('UserManagement/AksesAplikasi/HakAksesMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'kode_nama_user'    => get_var('kode_nama_user'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = HakAksesMdl::list($data, true)->RecordCount();
        $rs = HakAksesMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "pid"           => encrypt($rs->fields['pid']),
                "nrp"           => $rs->fields['nrp'],
                "nama_lengkap"  => $rs->fields['nama_lengkap'],
                "username"      => $rs->fields['username'],
                //"email"         => $rs->fields['email'],
                "is_active"     => $rs->fields['is_active'],
                'akses_txt'     => get_status_aktif($rs->fields['is_active'], 'txt'),
                'akses_css'     => get_status_aktif($rs->fields['is_active'], 'css'),
                'akses_icon'    => get_status_aktif($rs->fields['is_active'], 'icon'),
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
        $is_aktif = 't';
        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        $opt_gudang_akses = self::option_gudang_akses();
        
        $opt_otorisasi_akses = self::option_otorisasi_akses();

        // $opt_approval_akses = self::option_approval_akses();

        $data_group_akses = self::data_group_akses();

        return view('user_management.akses_aplikasi.hak_akses.create', compact(
            'chk_aktif',
            'txt_aktif',
            'opt_gudang_akses',
            'opt_otorisasi_akses',
            // 'opt_approval_akses',
            'data_group_akses'
        ));
    } /*}}}*/

    function cari_pegawai_get () /*{{{*/
    {
        $rs = HakAksesMdl::cari_pegawai();

        $dtJSON = array();

        while (!$rs->EOF)
        {
            $dtJSON[] = $rs->fields;

            $rs->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    function cek_user_post ($kode) /*{{{*/
    {
        $res = HakAksesMdl::cek_user($kode);

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
        $msg = HakAksesMdl::save_hak_akses();

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

    static function option_gudang_akses ($data = '') /*{{{*/
    {
        $data_akses = $data ? explode(',', $data) : [];

        $rs_gudang = HakAksesMdl::list_gudang_aktif();

        $opt_gudang_akses = '';
        while (!$rs_gudang->EOF)
        {
            $sel = in_array($rs_gudang->fields['gid'], $data_akses) ? 'selected=""' : '';

            $opt_gudang_akses .= "<option value='".$rs_gudang->fields['gid']."' ".$sel.">".$rs_gudang->fields['nama_gudang']."</option>\n";

            $rs_gudang->MoveNext();
        }

        return $opt_gudang_akses;
    } /*}}}*/

    static function option_otorisasi_akses ($data = '') /*{{{*/
    {
        $data_akses = $data ? explode(',', $data) : [];

        $rs_otorisasi = HakAksesMdl::list_otorisasi_aktif();

        $opt_otorisasi_akses = '';
        while (!$rs_otorisasi->EOF)
        {
            $sel = in_array($rs_otorisasi->fields['otogid'], $data_akses) ? 'selected=""' : '';

            $opt_otorisasi_akses .= "<option value='".$rs_otorisasi->fields['otogid']."' ".$sel.">".$rs_otorisasi->fields['description']."</option>\n";

            $rs_otorisasi->MoveNext();
        }

        return $opt_otorisasi_akses;
    } /*}}}*/

    static function option_approval_akses ($data = '') /*{{{*/
    {
        $data_akses = $data ? explode(',', $data) : [];

        $rs_approval = HakAksesMdl::list_approval_aktif();

        $opt_approval_akses = '';
        while (!$rs_approval->EOF)
        {
            $sel = in_array($rs_approval->fields['lilid'], $data_akses) ? 'selected=""' : '';

            $opt_approval_akses .= "<option value='".$rs_approval->fields['lilid']."' ".$sel.">[ ".$rs_approval->fields['lt_kode']." ] ".$rs_approval->fields['level_name']."</option>\n";

            $rs_approval->MoveNext();
        }

        return $opt_approval_akses;
    } /*}}}*/

    static function data_group_akses ($data = '') /*{{{*/
    {
        $data_akses = $data ? explode(',', $data) : [];

        $rs_group_akses = HakAksesMdl::list_group_akses_aktif();

        $data_group_akses = [];
        while (!$rs_group_akses->EOF)
        {
            $rs_group_akses->fields['check'] = in_array($rs_group_akses->fields['rgid'], $data_akses) ? 'checked=""' : '';

            $data_group_akses[] = FieldsToObject($rs_group_akses->fields);

            $rs_group_akses->MoveNext();
        }

        return $data_group_akses;
    } /*}}}*/

    public function edit_get () /*{{{*/
    {
        $fields = get_var('fields');
        $pid = get_var('pid', 0, 't');

        $rs_akses = HakAksesMdl::hak_akses_detail($pid);

        $data_akses = FieldsToObject($rs_akses->fields);

        $chk_admin_t = $data_akses->is_admin == 't' ? 'checked=""' : '';
        $chk_admin_f = $data_akses->is_admin == 'f' ? 'checked=""' : '';

        $chk_aktif = $data_akses->is_active == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($data_akses->is_active);

        $opt_gudang_akses = self::option_gudang_akses($data_akses->user_gudang);

        $opt_otorisasi_akses = self::option_otorisasi_akses($data_akses->user_otorisasi);

        // $opt_approval_akses = self::option_approval_akses($data_akses->user_approval);

        $data_group_akses = self::data_group_akses($data_akses->user_group);

        return view('user_management.akses_aplikasi.hak_akses.edit', compact(
            'data_akses',
            'chk_admin_t',
            'chk_admin_f',
            'chk_aktif',
            'txt_aktif',
            'opt_gudang_akses',
            'opt_otorisasi_akses',
            // 'opt_approval_akses',
            'data_group_akses'
        ));
    } /*}}}*/

    public function update_patch () /*{{{*/
    {
        $msg = HakAksesMdl::update_hak_akses();

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

    public function edit_pass_get () /*{{{*/
    {
        $fields = get_var('fields');
        $pid = get_var('pid', 0, 't');

        $rs_akses = HakAksesMdl::hak_akses_detail($pid);

        $data_akses = FieldsToObject($rs_akses->fields);

        $clue = decrypt($data_akses->clue);

        return view('user_management.akses_aplikasi.hak_akses.edit_pass', compact(
            'data_akses',
            'clue'
        ));
    } /*}}}*/

    public function update_pass_patch () /*{{{*/
    {
        $msg = HakAksesMdl::update_pass();

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