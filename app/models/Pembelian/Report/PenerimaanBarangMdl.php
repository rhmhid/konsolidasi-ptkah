<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PenerimaanBarangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $gid = $data['gid'];
        $suppid = $data['suppid'];
        $kode_nama_brg = strtolower(trim($data['kode_nama_brg']));

        if ($gid != '') $addsql .= " AND b.gid = ".$gid;

        if ($suppid != '') $addsql .= " AND b.suppid = ".$suppid;

        if ($kode_nama_brg != '') $addsql .= " AND LOWER(c.kode_brg || c.nama_brg) LIKE LOWER('%$kode_nama_brg%')";

        $addsql .= getAksesGudang('b.gid');

        $addsql .= " AND b.bid = ".$bid;

        $sql = "SELECT b.grid, b.grcode, b.grdate, b.no_faktur, NULL AS pocode
                    , d.nama_gudang, e.nama_supp, c.kode_brg, c.nama_brg
                    , f.nama_satuan, a.vol, a.harga, a.disc_rp, a.subtotal
                FROM good_receipt_d a
                INNER JOIN good_receipt b ON b.grid = a.grid
                INNER JOIN m_barang c ON c.mbid = a.mbid
                INNER JOIN m_gudang d ON d.gid = b.gid
                INNER JOIN m_supplier e ON e.suppid = b.suppid
                INNER JOIN m_satuan f ON f.kode_satuan = a.kode_satuan
                -- LEFT JOIN purchase_order g ON b.poid = g.poid
                WHERE DATE(b.grdate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY b.grdate DESC, c.kode_brg";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>