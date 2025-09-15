<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class DistribusiBarangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/DistribusiBarangMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'gid'           => get_var('gid'),
            'reff_gid'      => get_var('reff_gid'),
            'kbid'          => get_var('kbid'),
            'kode_nama'     => get_var('kode_nama'),
            'kode_trans'    => get_var('kode_trans'),
            'is_konfirm'    => get_var('is_konfirm'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = DistribusiBarangMdl::list($data, true)->RecordCount();
        $rs = DistribusiBarangMdl::list($data, false, $data['start'], $data['length']);

        $_old_transfer_code = "";
        while (!$rs->EOF)
        {
            $button_active = $_old_transfer_code != $rs->fields['transfer_code'] ? 't' : 'f';

            $record[] = array(
                'tbid'              => $rs->fields['tbid'],
                'transfer_date'     => $button_active == 'f' ? '' : dbtstamp2stringlong_ina($rs->fields['transfer_date']),
                'transfer_code'     => $button_active == 'f' ? '' : $rs->fields['transfer_code'],
                'barang'            => $rs->fields['kode_brg'].' - '.$rs->fields['nama_brg'],
                'vol'               => floatval($rs->fields['vol']).' '.$rs->fields['kode_satuan'],
                'ket_item'          => $rs->fields['ket_item'],
                'pengirim'          => $button_active == 'f' ? '' : $rs->fields['pengirim'],
                'penerima'          => $button_active == 'f' ? '' : $rs->fields['penerima'],
                'user_input'        => $button_active == 'f' ? '' : $rs->fields['user_input'],
                'status_konfirm'    => intval($rs->fields['kbid']),
                'button_active'     => $button_active,
            );

            $_old_transfer_code = $rs->fields['transfer_code'];

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

        return view('inventori.distribusi_barang.create', compact(
            'cmb_gudang_from',
            'cmb_gudang_to'
        ));
    } /*}}}*/

    public function cari_barang_get () /*{{{*/
    {
        $res = DistribusiBarangMdl::cari_barang();

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
        $msg = DistribusiBarangMdl::save();

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
        $msg = DistribusiBarangMdl::delete_trans($myid);

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