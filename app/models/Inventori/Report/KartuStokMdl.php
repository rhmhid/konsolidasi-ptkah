<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KartuStokMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function stock_awal ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = '';
        $sdate = $data['sdate'];
        $gid = $data['gid'];
        $mbid = $data['mbid'];

        if ($gid != '') $addsql .= " AND a.gid = ".$gid;

        if ($mbid != '') $addsql .= " AND a.mbid = ".$mbid;

        $addsql .= getAksesGudang('b.gid');

        $addsql .= " AND b.bid = ".$bid;

        $sql = "SELECT COALESCE(SUM(a.vol * a.isikecil), 0) AS awal
                FROM inventory_d a
                INNER JOIN inventory b ON b.invid = a.invid
                INNER JOIN journal_type c ON c.jtid = b.jtid
                INNER JOIN konversi_satuan d ON d.mbid = a.mbid AND d.kode_satuan = a.kode_satuan
                INNER JOIN m_barang e ON e.mbid = a.mbid
                INNER JOIN m_kategori_barang f ON f.kbid = e.kbid
                WHERE DATE(a.invdate) < ?
                    $addsql";
        $res = DB::GetOne($sql, [$sdate]);

        return $res;
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $gid = $data['gid'];
        $mbid = $data['mbid'];

        if ($gid != '') $addsql .= " AND a.gid = ".$gid;

        if ($mbid != '') $addsql .= " AND a.mbid = ".$mbid;

        $addsql .= getAksesGudang('b.gid');

        $addsql .= " AND b.bid = ".$bid;

        $sql = "SELECT c.journal_name, b.invdate, a.detailnote, e.kode_brg, e.nama_brg, e.kode_satuan
                    , SUM(CASE WHEN b.jtid NOT IN (5, 6) AND a.vol > 0 THEN a.vol * a.isikecil END) AS masuk
                    , SUM(CASE WHEN b.jtid NOT IN (5, 6) AND a.vol < 0 THEN a.vol * a.isikecil END) AS keluar
                    , SUM(CASE WHEN b.jtid IN (5, 6) THEN a.vol * a.isikecil END) AS adj
                FROM inventory_d a
                INNER JOIN inventory b ON b.invid = a.invid
                INNER JOIN journal_type c ON c.jtid = b.jtid
                INNER JOIN konversi_satuan d ON d.mbid = a.mbid AND d.kode_satuan = a.kode_satuan
                INNER JOIN m_barang e ON e.mbid = a.mbid
                INNER JOIN m_kategori_barang f ON f.kbid = e.kbid
                WHERE DATE(b.invdate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                GROUP BY c.journal_name, b.invdate, a.detailnote, e.kode_brg, e.nama_brg, e.kode_satuan
                ORDER BY b.invdate";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>