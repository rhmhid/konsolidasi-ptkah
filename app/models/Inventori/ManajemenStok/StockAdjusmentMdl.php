<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class StockAdjusmentMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $gid = $data['gid'];
        $kbid = $data['kbid'];
        $kode_nama = strtolower(trim($data['kode_nama']));

        if ($gid) $addsql .= " AND a.gid = ".$gid;

        if ($kbid) $addsql .= " AND a.kbid = ".$kbid;

        if ($kode_nama) $addsql .= " AND LOWER(a.kode_brg || a.nama_brg) ILIKE '%$kode_nama%'";

        $addsql .= getAksesGudang('a.gid');

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.aid, c.nama_gudang, a.adjdate, a.adjcode, b.kode_brg
                    , b.nama_brg, b.kode_satuan, a.stock AS bstok
                    , a.vol, d.nama_lengkap AS user, a.keterangan
                FROM adjusment a
                INNER JOIN m_barang b ON b.mbid = a.mbid
                INNER JOIN m_gudang c ON c.gid = a.gid
                INNER JOIN person d ON d.pid = a.create_by
                WHERE DATE(a.adjdate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY a.adjdate DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function delete_adj ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "DELETE FROM adjusment WHERE bid = ? AND aid = ?";
        $ok = DB::Execute($sql, [$bid, $myid]);

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            return 'Debug Sql Mode';
        }

        if ($ok)
        {
            DB::CommitTrans();
            return 'true';
        }
        else
        {
            $errmsg = "sql error : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/

    public static function list_stock ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $gid = $data['gid'];
        $kbid = $data['kbid'];
        $mbid = $data['mbid'];
        $kode_nama = strtolower(trim($data['kode_nama']));

        if ($kbid) $addsql .= " AND a.kbid = ".$kbid;

        if ($mbid) $addsql .= " AND a.mbid = ".$mbid;

        if ($kode_nama) $addsql .= " AND LOWER(a.kode_brg || a.nama_brg) ILIKE '%$kode_nama%'";

        if ($gid != 0)
            $sql = "SELECT a.mbid, a.kode_brg, a.nama_brg, a.kode_satuan, b.nama_kategori
                        , c.gid AS kode_gk, c.nama_gudang AS nama_gk
                        , get_item_qty(a.mbid, a.kode_satuan, $gid, DATE(NOW()), FALSE, $bid) AS stock
                    FROM m_barang a
                    INNER JOIN m_kategori_barang b ON b.kbid = a.kbid
                    INNER JOIN m_gudang c ON c.gid = $gid
                    INNER JOIN branch_assign d ON d.base_id = a.mbid AND d.item_type = 4
                    WHERE a.is_aktif = 't' AND a.hna > 0 AND d.bid = $bid $addsql
                    ORDER BY a.kode_brg";
        else
            $sql = "SELECT * FROM m_barang a WHERE 1 = 2 ORDER BY a.kode_brg";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function save () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $adjdate = get_var('pcdate', date('d-m-Y H:i'));
        $gid = get_var('gid', 0);
        $mbid = get_var('mbid', 0);
        $kode_satuan = get_var('kode_satuan');
        $stok_available = get_var('stok_available', 0);
        $vol_adj = get_var('vol_adj', 0);
        $keterangan = get_var('keterangan');
        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['adjdate']      = $adjdate;
        $record['gid']          = $gid;
        $record['mbid']         = $mbid;
        $record['kode_satuan']  = $kode_satuan;
        $record['stock']        = $stok_available;
        $record['vol']          = $vol_adj;
        $record['wac']          = DB::GetOne("SELECT get_item_wac(?, DATE(?), ?) AS wacc", [$mbid, $adjdate, $bid]);
        $record['keterangan']   = $keterangan;
        $record['is_posted']    = 't';
        $record['bid']          = $bid;
        $record['create_by']    = $record['modify_by'] = $pid;

        $sql = "SELECT * FROM adjusment WHERE 1 = 2";
        $rs = DB::Execute($sql);
        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            return 'Debug Sql Mode';
        }

        if ($ok)
        {
            DB::CommitTrans();
            return 'true';
        }
        else
        {
            $errmsg = "sql error : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/
}
?>