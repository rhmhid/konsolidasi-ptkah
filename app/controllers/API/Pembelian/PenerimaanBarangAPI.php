<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PenerimaanBarangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Pembelian/PenerimaanBarangMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'gid'           => get_var('gid'),
            'suppid'        => get_var('suppid'),
            'grcode'        => get_var('grcode'),
            'pocode'        => get_var('pocode'),
            'kode_nama'     => get_var('kode_nama'),
            'no_faktur'     => get_var('no_faktur'),
            'keterangan'    => get_var('keterangan'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = PenerimaanBarangMdl::list($data, true)->RecordCount();
        $rs = PenerimaanBarangMdl::list($data, false, $data['start'], $data['length']);

        $grn = "";
        while (!$rs->EOF)
        {
            if ($grn != $rs->fields['grcode'])
            {
                $grdate = dbtstamp2stringlong_ina($rs->fields['grdate']);
                $grcode = "Kode : ".$rs->fields['grcode'];
                $no_faktur = $rs->fields['no_faktur'];
                $pocode = "Kode : ".$rs->fields['pocode'];
                $nama_supp = $rs->fields['nama_supp'];
                $nama_gudang = "Gudang : ".$rs->fields['nama_gudang'];
                $useri = $rs->fields['useri'];
            }
            else
                $grdate = $grcode = $no_faktur = $pocode = $nama_supp = $nama_gudang = $useri = "";

            $record[] = array(
                'grid'          => $rs->fields['grid'],
                'grdate'        => $grdate,
                'grcode'        => $grcode,
                'no_faktur'     => $no_faktur,
                'asal_brg'      => $rs->fields['asal_brg'],
                'pocode'        => $pocode,
                'nama_supp'     => $nama_supp,
                'nama_gudang'   => $nama_gudang,
                'barang'        => $rs->fields['barang'],
                'jumlah'        => floatval($rs->fields['vol']).' '.$rs->fields['nama_satuan'],
                'harga'         => format_uang($rs->fields['harga'], 2),
                'user_input'    => $useri,
            );

            $grn = $rs->fields['grcode'];

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
        $grid = get_var('grid', 0);
        $AddBarang = '';

        $rsd = PenerimaanBarangMdl::detail_data($grid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->grdate = date('d-m-Y H:i:s', strtotime($data_head->grdate));

            $data_head->duedate = date('d-m-Y', strtotime($data_head->duedate));

            $data_head->tgl_faktur = date('d-m-Y', strtotime($data_head->tgl_faktur));

            $data_head->diskon_final_persen = floatval($data_head->diskon_final_persen);

            $data_head->diskon_final = floatval($data_head->diskon_final);

            $data_head->subtot = floatval($data_head->subtot);

            $data_head->ongkir = floatval($data_head->ongkir);

            $data_head->materai = floatval($data_head->materai);

            $data_head->ppn_persen = floatval($data_head->ppn_persen);

            $data_head->ppn_rp = floatval($data_head->ppn_rp);

            $data_head->other_cost = floatval($data_head->other_cost);

            $data_head->totalall = floatval($data_head->totalall);

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $mbid = $data_db->mbid;
                $barang = $data_db->barang;
                $kode_brg = $data_db->kode_brg;
                $nama_brg = $data_db->nama_brg;
                $kode_satuan = $data_db->kode_satuan;
                $all_satuan = $data_db->all_satuan;
                $is_bonus = $data_db->is_bonus;
                $harga_dasar = floatval($data_db->harga_dasar);
                $harga = floatval($data_db->harga);
                $exp_date = date('d-m-Y', strtotime($data_db->exp_date));
                $no_batch = $data_db->no_batch;
                $jml_terima = floatval($data_db->vol);
                $disc = floatval($data_db->disc);
                $disc_rp = floatval($data_db->disc_rp);
                $subtotal = floatval($data_db->subtotal);
                $grdid = $data_db->grdid;

                $AddBarang .= "AddBarang ('$mbid', '$barang', '$kode_brg', '$nama_brg', '$kode_satuan', '$all_satuan', '$harga_dasar', '$harga', '$is_bonus', '$exp_date', '$no_batch', '$jml_terima', '$disc', '$disc_rp', '$subtotal', '$grdid')\n";

                $rsd->MoveNext();
            }

            $AddBarang .= "FormatMoney()\n";
            $AddBarang .= "calcTotal()\n";
        }
        else
        {
            $data_head = New stdClass();

            $data_head->grid = $grid;

            $data_head->grdate = date('d-m-Y H:i:s');

            $data_head->asal_brg = 2;

            $data_head->cara_beli = 2;
        }
        
        $rs_gudang = Modules::data_gudang_besar();
        $cmb_gudang = $rs_gudang->GetMenu2('gid', $data_head->gid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="gid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $rs_supplier = Modules::data_supplier();
        $cmb_supplier = $rs_supplier->GetMenu2('suppid', $data_head->suppid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="suppid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..." required=""');

        $chk_asal_brg1 = $data_head->asal_brg == 1 ? 'checked=""' : "";
        $chk_asal_brg2 = $data_head->asal_brg == 2 ? 'checked=""' : "";

        $chk_is_medis_t = $data_head->is_medis == 't' ? 'checked=""' : "";
        $chk_is_medis_f = $data_head->is_medis == 'f' ? 'checked=""' : "";

        $chk_cara_beli1 = $data_head->cara_beli == 1 ? 'checked=""' : "";
        $chk_cara_beli2 = $data_head->cara_beli == 2 ? 'checked=""' : "";

        $rs_bank = Modules::data_bank();
        $cmb_bank = $rs_bank->GetMenu2('bank_id', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('pembelian.penerimaan_barang.form', compact(
            'data_head',
            'cmb_gudang',
            'cmb_supplier',
            'chk_asal_brg1',
            'chk_asal_brg2',
            'chk_is_medis_t',
            'chk_is_medis_f',
            'chk_cara_beli1',
            'chk_cara_beli2',
            'cmb_bank',
            'AddBarang'
        ));
    } /*}}}*/

    public function cari_barang_get () /*{{{*/
    {
        $res = PenerimaanBarangMdl::data_barang();

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
        $msg = PenerimaanBarangMdl::save();

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
        $msg = PenerimaanBarangMdl::delete_trans($myid);

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