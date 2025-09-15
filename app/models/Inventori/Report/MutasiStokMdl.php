<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class MutasiStokMdl extends DB
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

        $sql = "SELECT COALESCE(SUM(a.vol * a.isikecil), 0) AS vol_awal
                    , COALESCE(SUM(a.amount), 0) AS amount_awal
                FROM inventory_d a
                INNER JOIN inventory b ON b.invid = a.invid
                INNER JOIN journal_type c ON c.jtid = b.jtid
                INNER JOIN konversi_satuan d ON d.mbid = a.mbid AND d.kode_satuan = a.kode_satuan
                INNER JOIN m_barang e ON e.mbid = a.mbid
                INNER JOIN m_kategori_barang f ON f.kbid = e.kbid
                WHERE DATE(a.invdate) < ?
                    $addsql";
        $res = DB::Execute($sql, [$sdate]);

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

        $sql = "SELECT c.journal_name, b.invdate, b.invcode, b.reff_code, e.kode_brg
                    , e.nama_brg, e.kode_satuan, g.nama_lengkap AS user, (a.wac / a.isikecil) AS wac
                    , h.nama_gudang AS pengirim, i.nama_gudang AS penerima
                    , (CASE WHEN a.vol < 0 THEN a.vol * a.isikecil END) AS vol_keluar
                    , (CASE WHEN a.vol < 0 THEN a.amount END) AS amount_keluar
                    , (CASE WHEN a.vol > 0 THEN a.vol * a.isikecil END) AS vol_masuk
                    , (CASE WHEN a.vol > 0 THEN a.amount END) AS amount_masuk
                FROM inventory_d a
                INNER JOIN inventory b ON b.invid = a.invid
                INNER JOIN journal_type c ON c.jtid = b.jtid
                INNER JOIN konversi_satuan d ON d.mbid = a.mbid AND d.kode_satuan = a.kode_satuan
                INNER JOIN m_barang e ON e.mbid = a.mbid
                INNER JOIN m_kategori_barang f ON f.kbid = e.kbid
                INNER JOIN person g ON g.pid = b.create_by
                INNER JOIN m_gudang h ON h.gid = (CASE WHEN b.jtid IN (7, 8) THEN b.gid ELSE a.gid END)
                LEFT JOIN m_gudang i ON (CASE WHEN b.jtid IN (7, 8) THEN b.reff_gid ELSE a.reff_gid END) = i.gid
                WHERE DATE(b.invdate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY b.invdate, a.invdid";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>