<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class BarangSupplierAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model(array('Inventori/Setup/BarangSupplierMdl'));
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_suppid'          => get_var('s_suppid'),
            's_kode_nama_brg'   => get_var('s_kode_nama_brg'),
            's_is_supp_utama'   => get_var('s_is_supp_utama'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = BarangSupplierMdl::list($data, true)->RecordCount();
        $rs = BarangSupplierMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'bsid'          => $rs->fields['bsid'],
                'kode_brg'      => $rs->fields['kode_brg'],
                'nama_brg'      => $rs->fields['nama_brg'],
                'satuan'        => $rs->fields['nama_satuan'],
                'harga'         => format_uang($rs->fields['harga']),
                'disc'          => floatval($rs->fields['disc']),
                'supplier'      => $rs->fields['nama_supp'],
                'is_supp_utama' => $rs->fields['is_supp_utama'],
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
        $bsid = get_var('bsid', 0);

        $rs = BarangSupplierMdl::barang_supplier_detail($bsid);

        if (!$rs->EOF)
        {
            $data_db = FieldsToObject($rs->fields);

            $data_db->barang = $data_db->kode_brg.' '.$data_db->nama_brg;

            $is_supp_utama = $data_db->is_supp_utama ?? 'f';

            $hide_brg_cmb = 'd-none';
        }
        else
        {
            $data_db = New stdClass();

            $data_db->bsid = $bsid;

            $is_supp_utama = 't';

            $hide_brg_cmb = '';
        }

        $rs_supp = Modules::data_supplier();
        $cmb_supp = $rs_supp->GetMenu2('suppid', $data_db->suppid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Supplier" required=""');

        $chk_supp = $is_supp_utama == 't' ? 'checked=""' : '';
        $txt_supp = $is_supp_utama == 't' ? 'Ya' : 'Tidak';

        echo "<pre>";
        print_r(DB::db()->ServerInfo(true));
        echo "</pre>";

        return view('inventori.setup.barang_supplier.form', compact(
            'cmb_supp',
            'data_db',
            'chk_supp',
            'txt_supp',
            'hide_brg_cmb'
        ));
    } /*}}}*/

    public function cari_barang_get () /*{{{*/
    {
        $res = BarangSupplierMdl::cari_barang();

        $dtJSON = array();
        while (!$res->EOF)
        {
            $res->fields['hna'] = floatval($res->fields['hna']);

            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = BarangSupplierMdl::save_data();

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

    public function delete_post ($myid) /*{{{*/
    {
        $msg = BarangSupplierMdl::delete_data($myid);

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