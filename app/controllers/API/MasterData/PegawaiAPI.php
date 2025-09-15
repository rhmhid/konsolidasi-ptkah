<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PegawaiAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/PegawaiMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'kode_nama_emp' => get_var('kode_nama_emp'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = PegawaiMdl::list($data, true)->RecordCount();
        $rs = PegawaiMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "pid"           => encrypt($rs->fields['pid']),
                "nrp"           => $rs->fields['nrp'],
                "nama_lengkap"  => $rs->fields['nama_lengkap'],
                "alamat_lengkap"        => $rs->fields['alamat_lengkap'],
                "ttl"           => $rs->fields['tempat_lahir'].', '.dbtstamp2stringina($rs->fields['tgl_lahir']),
                "handphone"     => $rs->fields['handphone'],
                "is_aktif"      => $rs->fields['is_aktif'],
                'status_txt'    => get_status_aktif($rs->fields['is_aktif']),
                'status_css'    => get_status_aktif($rs->fields['is_aktif'], 'css'),
                'status_icon'   => get_status_aktif($rs->fields['is_aktif'], 'icon'),
                "is_dokter"      => $rs->fields['is_dokter'],
                'dokter_txt'    => get_status_dokter($rs->fields['is_dokter']),
                'dokter_css'    => get_status_dokter($rs->fields['is_dokter'], 'css'),
                'dokter_icon'   => get_status_dokter($rs->fields['is_dokter'], 'icon'),
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
        $pid = get_var('pid', 0, 't');

        $rsd = PegawaiMdl::detail_pegawai($pid);

        if (!$rsd->EOF)
        {
            $data_emp = FieldsToObject($rsd->fields);

            $data_emp->tgl_lahir = $data_emp->tgl_lahir == '' ? '' : date('d-m-Y', strtotime($data_emp->tgl_lahir));

            $sex = $data_emp->jenis_kelamin ?? '';

            $is_aktif = $data_emp->is_aktif ?? 'f';
            $is_dokter = $data_emp->is_dokter ?? 'f';
        }
        else
        {
            $data_emp = New stdClass();

            $data_emp->agama = '';

            $data_emp->status_pernikahan = '';

            $sex = '';

            $is_aktif = 't';
            $is_dokter = 'f';
        }

        $rs_jabatan = Modules::data_jabatan();
        $cmb_jabatan = $rs_jabatan->GetMenu2('mjid', $data_emp->mjid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="mjid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');


        $rs_divisi = Modules::data_divisi();
        $cmb_unit = $rs_divisi->GetMenu2('divid', $data_emp->divid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="divid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_tenaga = Modules::data_tenaga();
        $cmb_tenaga = $rs_tenaga->GetMenu2('mtid', $data_emp->mtid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="mtid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');


        $chk_sex_m = $sex == 'm' ? 'checked=""' : '';
        $chk_sex_f = $sex == 'f' ? 'checked=""' : '';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        $chk_dokter = $is_dokter == 't' ? 'checked=""' : '';
        $txt_dokter = get_status_dokter($is_dokter);

        return view('master_data.pegawai.form', compact(
            'data_emp',
            'chk_sex_m',
            'chk_sex_f',
            'cmb_unit',
            'cmb_jabatan',
            'cmb_tenaga',
            'chk_aktif',
            'txt_aktif',
            'chk_dokter',
            'txt_dokter'
        ));
    } /*}}}*/

    function cek_kode_post ($kode) /*{{{*/
    {
        $res = PegawaiMdl::cek_kode($kode);

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
        $msg = PegawaiMdl::save_pegawai();

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
