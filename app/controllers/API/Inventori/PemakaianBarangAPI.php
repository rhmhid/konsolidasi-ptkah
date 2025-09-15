<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PemakaianBarangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/PemakaianBarangMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'gid'       => get_var('gid'),
            'reff_gid'  => get_var('reff_gid'),
            'kbid'      => get_var('kbid'),
            'kode_nama' => get_var('kode_nama'),
            'kode_ciu'  => get_var('kode_ciu'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = PemakaianBarangMdl::list($data, true)->RecordCount();
        $rs = PemakaianBarangMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'ciuid'         => $rs->fields['ciuid'],
                'ciu_date'      => dbtstamp2stringlong_ina($rs->fields['ciu_date']),
                'ciu_code'      => $rs->fields['ciu_code'],
                'barang'        => $rs->fields['kode_brg'].' - '.$rs->fields['nama_brg'],
                'vol'           => floatval($rs->fields['vol']).' '.$rs->fields['kode_satuan'],
                'pengirim'      => $rs->fields['pengirim'],
                'penerima'      => $rs->fields['penerima'],
                'ket_item'      => $rs->fields['ket_item'],
                'user_input'    => $rs->fields['user_input'],
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
        $rs_gudang = Modules::data_gudang2();
        $cmb_gudang_from = $rs_gudang->GetMenu2('gid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-gudang" id="gid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_gudang->MoveFirst();
        $cmb_gudang_to = $rs_gudang->GetMenu2('reff_gid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-gudang" id="reff_gid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_gudang = Modules::data_coa_ciu();
        $cmb_coa_ciu = $rs_gudang->GetMenu2('coaid_cogs', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-gudang" id="coaid_cogs" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('inventori.pemakaian_barang.create', compact(
            'cmb_gudang_from',
            'cmb_gudang_to',
            'cmb_coa_ciu'
        ));
    } /*}}}*/

    public function cari_barang_get () /*{{{*/
    {
        $res = PemakaianBarangMdl::cari_barang();

        $dtJSON = array();
        while (!$res->EOF)
        {
            $res->fields['stock_from'] = floatval($res->fields['stock_from']);            
            $res->fields['stock_to'] = floatval($res->fields['stock_to']);            

            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = PemakaianBarangMdl::save();

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
        $msg = PemakaianBarangMdl::delete_data($myid);

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