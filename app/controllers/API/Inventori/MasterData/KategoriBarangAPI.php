<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class KategoriBarangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/MasterData/KategoriBarangMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_kode_nama_kate'  => get_var('s_kode_nama_kate'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = KategoriBarangMdl::list($data, true)->RecordCount();
        $rs = KategoriBarangMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'kbid'              => $rs->fields['kbid'],
                'kode_kategori'     => $rs->fields['kode_kategori'],
                'nama_kategori'     => $rs->fields['nama_kategori'],
                'coa_inv'           => $rs->fields['coa_inv'],
                'coa_cogs'          => $rs->fields['coa_cogs'],
                'is_medis'          => $rs->fields['is_medis'],
                'is_freeze'         => $rs->fields['is_freeze'],
                'is_sales'          => $rs->fields['is_sales'],
                'is_fixed_asset'    => $rs->fields['is_fixed_asset'],
                'is_konsinyasi'     => $rs->fields['is_konsinyasi'],
                'is_service'        => $rs->fields['is_service'],
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

    public function form_get () /*{{{*/
    {
        $kbid = get_var('kbid', 0);

        $rs = KategoriBarangMdl::kategori_barang_detail($kbid);

        if (!$rs->EOF)
        {
            $data_kel_brg = FieldsToObject($rs->fields);

            $is_medis = $data_kel_brg->is_medis ?? 'f';

            $is_freeze = $data_kel_brg->is_freeze ?? 'f';

            $is_sales = $data_kel_brg->is_sales ?? 'f';

            $is_fixed_asset = $data_kel_brg->is_fixed_asset ?? 'f';

            $is_aktif = $data_kel_brg->is_aktif ?? 'f';

            $is_konsinyasi = $data_kel_brg->is_konsinyasi ?? 'f';

            $is_service = $data_kel_brg->is_service ?? 'f';
        }
        else
        {
            $data_kel_brg = New stdClass();

            $data_kel_brg->kbid = $kbid;

            $is_medis = '';

            $is_freeze = '';

            $is_sales = '';

            $is_fixed_asset = '';

            $is_aktif = 't';

            $is_konsinyasi = '';

            $is_service = '';
        }

        $opt_length_kode_brg = get_combo_option_year($data_kel_brg->length_format_kode_brg, 1, 10);

        $rs_coa_inv = Modules::setup_coa_inv();
        $cmb_coa_inv = $rs_coa_inv->GetMenu2('coaid_inv', $data_kel_brg->coaid_inv, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_inv" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_coa_sales = KategoriBarangMdl::data_coa(4);
        $cmb_coa_sales = $rs_coa_sales->GetMenu2('coaid_sales', $data_kel_brg->coaid_sales, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_sales" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_sales = KategoriBarangMdl::data_coa(4);
        $cmb_coa_sales_inpatient = $rs_coa_sales->GetMenu2('coaid_sales_inpatient', $data_kel_brg->coaid_sales_inpatient, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_sales_inpatient" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_cogs = KategoriBarangMdl::data_coa(5);
        $cmb_coa_cogs = $rs_coa_cogs->GetMenu2('coaid_cogs', $data_kel_brg->coaid_cogs, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_cogs" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_cogs = KategoriBarangMdl::data_coa(5);
        $cmb_coa_cogs_inpatient = $rs_coa_cogs->GetMenu2('coaid_cogs_inpatient', $data_kel_brg->coaid_cogs_inpatient, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_cogs_inpatient" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_cogs->MoveFirst();
        $cmb_coa_adj = $rs_coa_cogs->GetMenu2('coaid_adj', $data_kel_brg->coaid_adj, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_adj" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_cogs->MoveFirst();
        $cmb_coa_so = $rs_coa_cogs->GetMenu2('coaid_so', $data_kel_brg->coaid_so, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_so" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_cogs->MoveFirst();
        $cmb_coa_ciu = $rs_coa_cogs->GetMenu2('coaid_ciu', $data_kel_brg->coaid_ciu, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_ciu" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $rs_coa_cogs->MoveFirst();
        $cmb_coa_cogs_ap_kons = $rs_coa_cogs->GetMenu2('coaid_cogs_ap_konsinyasi', $data_kel_brg->coaid_cogs_ap_konsinyasi, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid_cogs_ap_konsinyasi" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        $chk_medis = $is_medis == 't' ? 'checked=""' : '';
        $chk_non_medis = $is_medis == 'f' ? 'checked=""' : '';
        $chk_freeze = $is_freeze == 't' ? 'checked=""' : '';
        $chk_sales = $is_sales == 't' ? 'checked=""' : '';
        $chk_fixed_asset = $is_fixed_asset == 't' ? 'checked=""' : '';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        $chk_konsinyasi = $is_konsinyasi == 't' ? 'checked=""' : '';
        $chk_service = $is_service == 't' ? 'checked=""' : '';

        return view('inventori.masterdata.kategori_barang.form', compact(
            'data_kel_brg',
            'opt_length_kode_brg',
            'cmb_coa_inv',
            'cmb_coa_sales',
            'cmb_coa_sales_inpatient',
            'cmb_coa_cogs',
            'cmb_coa_cogs_inpatient',
            'cmb_coa_adj',
            'cmb_coa_so',
            'cmb_coa_ciu',
            'cmb_coa_cogs_ap_kons',
            'chk_medis',
            'chk_non_medis',
            'chk_freeze',
            'chk_sales',
            'chk_fixed_asset',
            'chk_aktif',
            'txt_aktif',
            'chk_konsinyasi',
            'chk_service'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = KategoriBarangMdl::cek_kode($kode);

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
        $msg = KategoriBarangMdl::save_kategori_barang();

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