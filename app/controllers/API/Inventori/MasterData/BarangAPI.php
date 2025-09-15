<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class BarangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model(array('Inventori/MasterData/BarangMdl'));
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_kbid'            => get_var('s_kbid'),
            's_kode_nama_brg'   => get_var('s_kode_nama_brg'),
            's_is_aktif'        => get_var('s_is_aktif'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = BarangMdl::list($data, true)->RecordCount();
        $rs = BarangMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'mbid'          => $rs->fields['mbid'],
                "mbidenc"       => encrypt($rs->fields['mbid']),
                'kode_brg'      => $rs->fields['kode_brg'],
                'nama_brg'      => $rs->fields['nama_brg'],
                'kel_brg'       => $rs->fields['kel_brg'],
                'satuan_kecil'  => $rs->fields['nama_satuan'],
                'satuan_besar'  => $rs->fields['satuan_besar'],
                'hna'           => format_uang($rs->fields['hna']),
                'hna_ppn'       => format_uang($rs->fields['hna_ppn']),
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
        $mbid = get_var('mbid', 0);

        $rs = BarangMdl::barang_detail($mbid);

        if (!$rs->EOF)
        {
            $data_brg = FieldsToObject($rs->fields);

            $is_aktif = $data_brg->is_aktif ?? 'f';

            $adds = '';

            $rss = BarangMdl::detail_barang_satuan($data_brg->mbid, $data_brg->kode_satuan);

            while (!$rss->EOF)
            {
                $sat_brg = $rss->fields['is_aktif'];

                $sat_brg_txt = $rss->fields['is_aktif'] == 't' ? 'Aktif' : 'Tidak Aktif';

                $adds .= "AddSatuan('".$rss->fields['kode_satuan']."', '".$rss->fields['nama_satuan']."', '".$rss->fields['isikecil']."', '".$sat_brg."', '".$sat_brg_txt."', '', ".$rss->fields['ksid'].")\n";

                $rss->MoveNext();
            }
        }
        else
        {
            $data_brg = New stdClass();

            $data_brg->mbid = $mbid;

            $data_brg->persen_hna = dataConfigs('default_ppn_beli');

            $data_brg->ppn_jual = dataConfigs('default_ppn_jual');

            $is_aktif = 't';

            $adds = '';
        }

        $rs_kel_brg = Modules::data_kel_brg();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('kbid', $data_brg->kbid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="kbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Kategori Barang" required=""');

        $rs_merk = Modules::data_merk();
        $cmb_merk = $rs_merk->GetMenu2('mmid', $data_brg->mmid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-md" id="mmid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Merk"');

        $rs_satuan = Modules::data_satuan();
        $cmb_satuan = $rs_satuan->GetMenu2('kode_satuan', $data_brg->kode_satuan, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-md" id="kode_satuan" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Satuan" required=""');

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('inventori.masterdata.barang.form', compact(
            'data_brg',
            'cmb_kel_brg',
            'cmb_merk',
            'cmb_satuan',
            'chk_aktif',
            'txt_aktif',
            'adds'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = BarangMdl::cek_kode($kode);

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

    public function barang_satuan_get () /*{{{*/
    {
        return view('inventori.masterdata.barang.barang_satuan');
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = BarangMdl::save_barang();

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