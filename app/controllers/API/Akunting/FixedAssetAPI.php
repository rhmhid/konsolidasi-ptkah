<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class FixedAssetAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/FixedAssetMdl');
    } /*}}}*/

    public function cek_kode_post ($mytype, $kode) /*{{{*/
    {
        $res = FixedAssetMdl::cek_kode($mytype, $kode);

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

    public function list_lokasi_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'kode_nama_lok' => get_var('kode_nama_lok'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = FixedAssetMdl::list_lokasi($data, true)->RecordCount();
        $rs = FixedAssetMdl::list_lokasi($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'falid'         => $rs->fields['falid'],
                'lokasi_kode'   => $rs->fields['lokasi_kode'],
                'lokasi_nama'   => $rs->fields['lokasi_nama'],
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

    public function form_lokasi_get () /*{{{*/
    {
        $falid = get_var('falid', 0);

        $rsd = FixedAssetMdl::lokasi_detail($falid);

        if (!$rsd->EOF)
        {
            $data_lok = FieldsToObject($rsd->fields);

            $is_aktif = $data_lok->is_aktif ?? 'f';
        }
        else
        {
            $data_lok = New stdClass();

            $data_lok->falid = $falid;

            $is_aktif = 't';
        }

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.fixed_asset.form_lokasi', compact(
            'data_lok',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function save_lokasi_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::save_lokasi();

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

    public function list_kategori_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'kode_nama_kate'    => get_var('kode_nama_kate'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = FixedAssetMdl::list_kategori($data, true)->RecordCount();
        $rs = FixedAssetMdl::list_kategori($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'facid'             => $rs->fields['facid'],
                'kode_kategori'     => $rs->fields['kode_kategori'],
                'nama_kategori'     => $rs->fields['nama_kategori'],
                'coa_fa'            => $rs->fields['coa_fa'],
                'coa_accumulated'   => $rs->fields['coa_accumulated'],
                'coa_depreciation'  => $rs->fields['coa_depreciation'],
                'is_aktif'          => $rs->fields['is_aktif'],
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

    public function form_kategori_get () /*{{{*/
    {
        $facid = get_var('facid', 0);

        $rsd = FixedAssetMdl::kategori_detail($facid);

        if (!$rsd->EOF)
        {
            $data_kate = FieldsToObject($rsd->fields);

            $is_aktif = $data_kate->is_aktif ?? 'f';

            $is_monthly = $data_kate->is_monthly ?? 't';
        }
        else
        {
            $data_kate = New stdClass();

            $data_kate->facid = $facid;

            $is_aktif = 't';

            $is_monthly = 't';
        }

        $chk_monthly_f = $is_monthly == 't' ? '' : 'checked=""';
        $chk_monthly_t = $is_monthly == 't' ? 'checked=""' : '';

        $rs_coa_fa = FixedAssetMdl::data_setup_coa(4);
        $cmb_coa_fa = $rs_coa_fa->GetMenu2('coaid_fa', $data_kate->coaid_fa, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_fa" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_dep_fa = FixedAssetMdl::data_setup_coa(5);
        $cmb_dep_fa = $rs_dep_fa->GetMenu2('coaid_accumulated', $data_kate->coaid_accumulated, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_accumulated" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_cogs_dfa = FixedAssetMdl::data_coa();
        $cmb_cogs_dfa = $rs_cogs_dfa->GetMenu2('coaid_depreciation', $data_kate->coaid_depreciation, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_depreciation" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_kel_brg = FixedAssetMdl::data_kategori_barang();
        $cmb_kel_brg = $rs_kel_brg->GetMenu2('kbid', $data_kate->kbid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-md" id="kbid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $opt_length_kode_fa = get_combo_option_year($data_kate->length_format_kode_fa, 2, 10);

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.fixed_asset.form_kategori', compact(
            'data_kate',
            'chk_monthly_f',
            'chk_monthly_t',
            'cmb_coa_fa',
            'cmb_dep_fa',
            'cmb_cogs_dfa',
            'cmb_kel_brg',
            'opt_length_kode_fa',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function save_kategori_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::save_kategori();

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

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'sdate'             => get_var('sdate', date('Y-m-d')),
            'edate'             => get_var('edate', date('Y-m-d')),
            'facid'             => get_var('facid'),
            'falid'             => get_var('falid'),
            'fastatus'          => get_var('fastatus'),
            'kode_nama_desc'    => get_var('kode_nama_desc'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = FixedAssetMdl::list($data, true)->RecordCount();
        $rs = FixedAssetMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $umur_thn = floor($rs->fields['masa_manfaat'] / 12);
            $umur_bln = ($rs->fields['masa_manfaat'] % 12);
            $masa_manfaat = "";

            if ($umur_thn <> 0) $masa_manfaat .= $umur_thn.' Tahun ';

            if ($umur_bln <> 0) $masa_manfaat .= $umur_bln.' Bulan';

            $record[] = array(
                'faid'              => $rs->fields['faid'],
                'facategory'        => $rs->fields['nama_kategori'],
                'facode'            => $rs->fields['facode'],
                'faname'            => $rs->fields['faname'],
                'masa_manfaat'      => $masa_manfaat,
                'nilai_perolehan'   => format_uang($rs->fields['nilai_perolehan'], 2),
                'fadate'            => dbtstamp2stringina($rs->fields['fadate']),
                'fadesc'            => $rs->fields['fadesc'],
                'falokasi'          => $rs->fields['lokasi_nama'],
                'fastatus'          => $rs->fields['fastatus'],
                'fastatus_notes'    => $rs->fields['fastatus_notes'],
                'wo_notes'          => $rs->fields['wo_notes'],
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
        $faid = get_var('faid', 0);
        $depresiasi_status = false;
        $data_depresiasi = [];

        $rsd = FixedAssetMdl::detail_asset($faid);

        if (!$rsd->EOF)
        {
            $data_db = FieldsToObject($rsd->fields);

            $data_db->fadate = $data_db->fadate ? date('d-m-Y', strtotime($data_db->fadate)) : "";

            $data_db->umur_thn = floor($data_db->masa_manfaat / 12);

            $data_db->umur_bln = ($data_db->masa_manfaat % 12);

            $is_monthly = $data_db->is_monthly ?? 't';

            $skip_depresiasi = $data_db->skip_depresiasi ?? 'f';

            $is_header = $data_db->is_header ?? 'f';

            if ($data_db->fastatus == 3 || $data_db->fastatus == 4)
            {
                $rs_depre = FixedAssetMdl::detail_depresiasi_asset($data_db->faid);

                $i = 0;
                $orig_amount = $rs_depre->fields['nilai_perolehan'];
                $regamount = 0;
                $old_amount = 0;

                while (!$rs_depre->EOF)
                {
                    if ($old_amount != 0 && $old_amount != $rs_depre->fields['nilai_perolehan'])
                    {
                        $i++;
                        $diffs = $rs_depre->fields['nilai_perolehan'] - $orig_amount;
                        $nilai_buku = $orig_amount + $diffs < 0 ? 0 : $orig_amount + $diffs;

                        $tmpdata = array();
                        $tmpdata['depre_date']      = dbtstamp2stringina($rs_depre->fields['depre_date']);
                        $tmpdata['depre_notes']     = '*** Revaluasi Asset ***';
                        $tmpdata['nilai_perolehan'] = '-';
                        $tmpdata['nilai_akumulasi'] = '-';
                        $tmpdata['nilai_buku']      = format_uang($nilai_buku, 2);

                        $data_depresiasi[$i] = $tmpdata;

                        $orig_amount = $rs_depre->fields['nilai_perolehan'];
                        $regamount = 0;
                    }

                    $old_amount = $rs_depre->fields['nilai_perolehan'];
                    $regamount += $rs_depre->fields['depre_amount'];
                    $nilai_buku = $rs_depre->fields['nilai_perolehan'] - $regamount;
                    $i++;

                    $tmpdata = array();
                    $tmpdata['depre_date']      = dbtstamp2stringina($rs_depre->fields['depre_date']);
                    $tmpdata['depre_notes']     = 'Penyusutan ke-' . $i;
                    $tmpdata['nilai_perolehan'] = format_uang($rs_depre->fields['nilai_perolehan'], 2);
                    $tmpdata['nilai_akumulasi'] = format_uang($regamount, 2);
                    $tmpdata['nilai_buku']      = format_uang($nilai_buku, 2);

                    $data_depresiasi[$i] = $tmpdata;

                    $val -= $rs_depre->fields['depre_amount'];
                    $depresiasi_status = true;

                    $rs_depre->MoveNext();
                }
            }
        }
        else
        {
            $data_db = New stdClass();

            $data_db->faid = $faid;

            $data_db->umur_thn = 0;

            $data_db->umur_bln = 0;

            $is_monthly = 't';

            $skip_depresiasi = 'f';

            $is_header = 'f';
        }

        $chk_monthly_thn = $is_monthly == 'f' ? 'checked=""' : '';
        $chk_monthly_bln = $is_monthly == 't' ? 'checked=""' : '';

        $chk_depresiasi = $skip_depresiasi == 't' ? 'checked=""' : '';

        $chk_header = $is_header == 't' ? 'checked=""' : '';

        $data_kategori_fa = Modules::data_kategori_fa();
        $cmb_kategori_fa = $data_kategori_fa->GetMenu2('facid', $data_db->facid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="facid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Kategori Asset..." required=""');

        $data_lokasi_fa = Modules::data_lokasi_fa();
        $cmb_lokasi_fa = $data_lokasi_fa->GetMenu2('falid', $data_db->falid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="falid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Lokasi Asset..." required=""');

        $data_cost_center = Modules::data_cost_center();
        $cmb_cost_center = $data_cost_center->GetMenu2('pccid', $data_db->pccid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="pccid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cost Center..." required=""');

        $data_header_fa = Modules::data_header_fa();
        $cmb_header_fa = $data_header_fa->GetMenu2('parent_faid', $data_db->parent_faid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="parent_faid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Header Asset..."');

        return view('akunting.fixed_asset.form', compact(
            'data_db',
            'cmb_kategori_fa',
            'cmb_lokasi_fa',
            'cmb_cost_center',
            'cmb_header_fa',
            'chk_monthly_thn',
            'chk_monthly_bln',
            'chk_depresiasi',
            'chk_header',
            'depresiasi_status',
            'data_depresiasi'
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::save_asset();

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

    public function approve_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::approve_asset();

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

    public function proses_depresiasi_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::proses_depresiasi();

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

    public function ubah_lokasi_get () /*{{{*/
    {
        $faid = get_var('faid', 0);

        $rsd = FixedAssetMdl::detail_asset($faid);

        if (!$rsd->EOF)
        {
            $data_db = FieldsToObject($rsd->fields);
        }
        else
        {
            $data_db = New stdClass();

            $data_db->faid = $faid;
        }

        $data_lokasi_fa = Modules::data_lokasi_fa();
        $cmb_lokasi_fa = $data_lokasi_fa->GetMenu2('falid_new', $data_db->falid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 sel-lokasi" id="falid_new" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Lokasi Asset..." required=""');

        $data_cost_center = Modules::data_cost_center();
        $cmb_cost_center = $data_cost_center->GetMenu2('pccid_new', $data_db->pccid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 sel-lokasi" id="pccid_new" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cost Center..." required=""');

        return view('akunting.fixed_asset.ubah_lokasi', compact(
            'data_db',
            'cmb_lokasi_fa',
            'cmb_cost_center',
        ));
    } /*}}}*/

    public function ubah_lokasi_histori_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'faid'      => get_var('faid'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = FixedAssetMdl::ubah_lokasi_histori($data, true)->RecordCount();
        $rs = FixedAssetMdl::ubah_lokasi_histori($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'faid'              => $rs->fields['faid'],
                'create_time'       => dbtstamp2stringlong_ina($rs->fields['create_time']),
                'lokasi_from'       => $rs->fields['lokasi_from'],
                'cost_center_from'  => $rs->fields['cost_center_from'],
                'lokasi_to'         => $rs->fields['lokasi_to'],
                'cost_center_to'    => $rs->fields['cost_center_to'],
                'notes'             => $rs->fields['notes'],
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

    public function save_ubah_lokasi_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::save_ubah_lokasi();

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

    public function revaluate_get () /*{{{*/
    {
        $faid = get_var('faid', 0);

        $rsd = FixedAssetMdl::detail_asset($faid);

        if (!$rsd->EOF)
        {
            $data_db = FieldsToObject($rsd->fields);

            $data_db->fadate = $data_db->fadate ? date('d-m-Y', strtotime($data_db->fadate)) : "";

            $data_db->umur_thn = floor($data_db->masa_manfaat / 12);

            $data_db->umur_bln = ($data_db->masa_manfaat % 12);

            $data_db->nilai_buku = floatval($data_db->nilai_perolehan);

            $rs_depre = FixedAssetMdl::detail_depresiasi_asset($data_db->faid);

            while (!$rs_depre->EOF)
            {
                $data_db->nilai_buku -= $rs_depre->fields['depre_amount'];

                $rs_depre->MoveNext();
            }
        }
        else
        {
            $data_db = New stdClass();

            $data_db->faid = $faid;

            $data_db->umur_thn = 0;

            $data_db->umur_bln = 0;
        }

        $data_cost_center = Modules::data_cost_center();
        $cmb_cost_center = $data_cost_center->GetMenu2('pccid', $data_db->pccid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="pccid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Cost Center..." required=""');

        return view('akunting.fixed_asset.revaluate', compact(
            'data_db',
            'cmb_cost_center'
        ));
    } /*}}}*/

    public function save_revaluate_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::save_revaluate_asset();

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

    public function write_off_get () /*{{{*/
    {
        $faid = get_var('faid', 0);
        $wo_date = date('d-m-Y H:i');

        $rsd = FixedAssetMdl::detail_asset($faid);

        if (!$rsd->EOF)
        {
            $data_db = FieldsToObject($rsd->fields);

            $data_db->fadate = $data_db->fadate ? date('d-m-Y', strtotime($data_db->fadate)) : "";

            $data_db->umur_thn = floor($data_db->masa_manfaat / 12);

            $data_db->umur_bln = ($data_db->masa_manfaat % 12);

            $data_db->nilai_buku = floatval($data_db->nilai_perolehan);

            $rs_depre = FixedAssetMdl::detail_depresiasi_asset($data_db->faid);

            while (!$rs_depre->EOF)
            {
                $data_db->nilai_buku -= $rs_depre->fields['depre_amount'];

                $rs_depre->MoveNext();
            }
        }
        else
        {
            $data_db = New stdClass();

            $data_db->faid = $faid;

            $data_db->umur_thn = 0;

            $data_db->umur_bln = 0;
        }

        $data_write_off = Modules::data_write_off();
        $cmb_write_off = $data_write_off->GetMenu2('wo_status', 1, false, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="wo_status" data-control="select2" data-placeholder="Pilih Perlakuan..." required=""');

        $data_bank = Modules::data_bank();
        $cmb_bank = $data_bank->GetMenu2('bank_id', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-placeholder="Pilih Bank..."');

        $data_coa = FixedAssetMdl::data_coa_write_off();
        $cmb_coa_write_off = $data_coa->GetMenu2('coa_write_off', $data_db->coaid_depreciation, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coa_write_off" data-control="select2" data-placeholder="Pilih C.O.A..." required=""');

        return view('akunting.fixed_asset.write_off', compact(
            'wo_date',
            'data_db',
            'cmb_write_off',
            'cmb_bank',
            'cmb_coa_write_off'
        ));
    } /*}}}*/

    public function save_write_off_patch () /*{{{*/
    {
        $msg = FixedAssetMdl::save_write_off_asset();

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