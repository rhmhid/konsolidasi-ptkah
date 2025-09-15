<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class StockAdjusmentAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/ManajemenStok/StockAdjusmentMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'gid'       => get_var('gid'),
            'kbid'      => get_var('kbid'),
            'kode_nama' => get_var('kode_nama'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = StockAdjusmentMdl::list($data, true)->RecordCount();
        $rs = StockAdjusmentMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'aid'           => $rs->fields['aid'],
                'gudang'        => $rs->fields['nama_gudang'],
                'adjdate'       => dbtstamp2stringlong_ina($rs->fields['adjdate']),
                'adjcode'       => $rs->fields['adjcode'],
                'kode_brg'      => $rs->fields['kode_brg'],
                'nama_brg'      => $rs->fields['nama_brg'],
                'kode_satuan'   => $rs->fields['kode_satuan'],
                'bstok'         => floatval($rs->fields['bstok']),
                'vol'           => floatval($rs->fields['vol']),
                'fstok'         => floatval($rs->fields['bstok']) + floatval($rs->fields['vol']),
                'keterangan'    => $rs->fields['keterangan'],
                'user_input'    => $rs->fields['user'],
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

    public function delete_post ($myid) /*{{{*/
    {
        $msg = StockAdjusmentMdl::delete_adj($myid);

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

    public function list_stock_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'gid'       => get_var('gid'),
            'kbid'      => get_var('kbid'),
            'kode_nama' => get_var('kode_nama'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = StockAdjusmentMdl::list_stock($data, true)->RecordCount();
        $rs = StockAdjusmentMdl::list_stock($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'gid'           => $data['gid'],
                'mbid'          => $rs->fields['mbid'],
                'kode_brg'      => $rs->fields['kode_brg'],
                'nama_brg'      => $rs->fields['nama_brg'],
                'kode_satuan'   => $rs->fields['kode_satuan'],
                'stock'         => floatval($rs->fields['stock']),
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
        $data = array(
            'gid'   => get_var('gid'),
            'mbid'  => get_var('mbid')
        );

        $rsd = StockAdjusmentMdl::list_stock($data);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        $data_db->stock = floatval($data_db->stock);

        return view('inventori.manajemen_stok.stock_adjusment.create', compact(
            'data_db',
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = StockAdjusmentMdl::save();

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