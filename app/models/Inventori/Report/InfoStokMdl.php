<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class InfoStokMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = $addsel = $addsql2 = "";
        $sdate = $data['sdate'];
        $coaid_inv = $data['coaid_inv'];
        $kbid = $data['kbid'];
        $gid = $data['gid'];
        $kode_nama = strtolower(trim($data['kode_nama']));

        if ($coaid_inv) $addsql .= " AND a.coaid = ".$coaid_inv;

        if ($kbid) $addsql .= " AND d.kbid = ".$kbid;

        if ($gid)
        {
            $addsel = ", SUM(CASE WHEN a.gid = $gid THEN (a.vol * a.isikecil) ELSE 0 END) AS stock
                        , ((CASE WHEN SUM(a.vol * a.isikecil) = 0 THEN 0 ELSE SUM(a.amount) / SUM(a.vol * a.isikecil) END) * SUM(CASE WHEN a.gid = $gid THEN (a.vol * a.isikecil) ELSE 0 END)) AS amount";

            $addsql2 .= " AND aa.wac <> 0 AND aa.stock <> 0";
        }
        else
        {
            $addsel = ", SUM(a.vol * a.isikecil) AS stock, SUM(a.amount) AS amount";

            $addsql2 = "";
        }

        if ($kode_nama) $addsql .= " AND LOWER(d.kode_brg || d.nama_brg) ILIKE LOWER('%$kode_nama%')";

        $addsql .= getAksesGudang('b.gid');

        $addsql .= " AND b.bid = ".$bid;

        $sql = "SELECT aa.mbid, aa.kode_brg, aa.nama_brg, aa.kode_satuan, aa.kel_brg, aa.is_aktif
                    , aa.stock, (CASE WHEN aa.stock = 0 THEN 0 ELSE aa.amount END) AS amount, aa.wac
                FROM (
                    SELECT a.mbid, d.kode_brg, d.nama_brg, d.kode_satuan, e.nama_kategori AS kel_brg
                        , d.is_aktif, (CASE WHEN SUM(a.vol * a.isikecil) = 0 THEN 0 ELSE SUM(a.amount) / SUM(a.vol * a.isikecil) END) AS wac
                        $addsel
                    FROM inventory_d a
                    INNER JOIN inventory b ON b.invid = a.invid
                    INNER JOIN konversi_satuan c ON c.mbid = a.mbid AND c.kode_satuan = a.kode_satuan
                    INNER JOIN m_barang d ON d.mbid = a.mbid
                    INNER JOIN m_kategori_barang e ON e.kbid = d.kbid
                    WHERE DATE(b.invdate) <= '$sdate' $addsql
                    GROUP BY a.mbid, d.kode_brg, d.nama_brg, d.kode_satuan, kel_brg, d.is_aktif
                ) aa
                WHERE 1 = 1 $addsql2
                ORDER BY aa.kode_brg";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function detail_stok ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = '';
        $mbid = $data['mbid'];
        $sdate = date('Y-m-d', strtotime($data['sdate']));

        if ($mbid != '') $addsql .= " AND a.mbid = ".$mbid;

        $addsql .= " AND b.bid = ".$bid;

        $sql = "SELECT d.kode_brg, d.nama_brg, d.kode_satuan, e.nama_kategori AS kel_brg,
                    d.is_aktif, f.nama_gudang, SUM(a.vol * a.isikecil) AS stock
                FROM inventory_d a
                INNER JOIN inventory b ON b.invid = a.invid
                INNER JOIN konversi_satuan c ON c.mbid = a.mbid AND c.kode_satuan = a.kode_satuan
                INNER JOIN m_barang d ON d.mbid = a.mbid
                INNER JOIN m_kategori_barang e ON e.kbid = d.kbid
                INNER JOIN m_gudang f ON f.gid = a.gid
                WHERE DATE(b.invdate) <= '$sdate' $addsql
                GROUP BY d.kode_brg, d.nama_brg, d.kode_satuan, kel_brg, d.is_aktif, f.nama_gudang
                ORDER BY d.kode_brg";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>
