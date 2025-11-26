<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class KonfirmasiDistribusiAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/KonfirmasiDistribusiMdl');
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

        $jmlbris = KonfirmasiDistribusiMdl::list($data, true)->RecordCount();
        $rs = KonfirmasiDistribusiMdl::list($data, false, $data['start'], $data['length']);

        $_old_trans_code = "";
        while (!$rs->EOF)
        {
            $button_active = $_old_trans_code != $rs->fields['trans_code'] ? 't' : 'f';

            $record[] = array(
                'trans_id'          => $rs->fields['trans_id'],
                'trans_date'        => $button_active == 'f' ? '' : dbtstamp2stringlong_ina($rs->fields['trans_date']),
                'trans_code'        => $button_active == 'f' ? '' : $rs->fields['trans_code'],
                'transfer_code'     => $button_active == 'f' ? '' : $rs->fields['transfer_code'],
                'barang'            => $rs->fields['kode_brg'].' - '.$rs->fields['nama_brg'],
                'vol'               => floatval($rs->fields['vol']).' '.$rs->fields['kode_satuan'],
                'ket_item'          => $rs->fields['ket_item'],
                'pengirim'          => $button_active == 'f' ? '' : $rs->fields['pengirim'],
                'penerima'          => $button_active == 'f' ? '' : $rs->fields['penerima'],
                'user_input'        => $button_active == 'f' ? '' : $rs->fields['user_input'],
                'button_active'     => $button_active,
                'is_konfirm'        => $rs->fields['is_konfirm'],
            );

            $_old_trans_code = $rs->fields['trans_code'];

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

    public function create_get ($myid) /*{{{*/
    {
        $AddBarang = '';

        $rsd = KonfirmasiDistribusiMdl::detail_data($myid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->transfer_date = date('d-m-Y H:i', strtotime($data_head->transfer_date));

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $tbdid = $data_db->tbdid;
                $kode_brg = $data_db->kode_brg;
                $nama_brg = $data_db->nama_brg;
                $barang = $data_db->kode_brg.' - '.$data_db->nama_brg;
                $kode_satuan = $data_db->kode_satuan;
                $vol_kirim = floatval($data_db->vol);

                $AddBarang .= "AddBarang($tbdid, '$barang', '$kode_satuan', '$vol_kirim')\n";

                $rsd->MoveNext();
            }
        }
        else
        {
            $data_head = New stdClass();

            $data_head->tbid = $myid;

            $data_head->transfer_date = date('d-m-Y H:i');
        }

        return view('inventori.konfirmasi_distribusi.create', compact(
            'data_head',
            'AddBarang'
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = KonfirmasiDistribusiMdl::save();

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
        $msg = KonfirmasiDistribusiMdl::delete_trans($myid);

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