<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class BarangSupplierMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $s_suppid = $data['s_suppid'];
        $s_kode_nama_brg = strtolower(trim($data['s_kode_nama_brg']));
        $s_is_supp_utama = $data['s_is_supp_utama'];

        if ($s_suppid) $addsql .= " AND a.suppid = ".$s_suppid;

        if ($s_kode_nama_brg) $addsql .= " AND LOWER(b.kode_brg || b.nama_brg) ILIKE '%$s_kode_nama_brg%'";

        if ($s_is_supp_utama) $addsql .= " AND a.is_supp_utama = '$s_is_supp_utama'";

        $sql = "SELECT a.bsid, a.mbid, b.kode_brg, b.nama_brg, c.nama_satuan
                    , a.harga, a.disc, d.nama_supp, a.is_supp_utama
                FROM barang_supplier a
                INNER JOIN m_barang b ON b.mbid = a.mbid
                INNER JOIN m_satuan c ON c.kode_satuan = a.kode_satuan
                INNER JOIN m_supplier d ON d.suppid = a.suppid
                WHERE 1 = 1 $addsql
                ORDER BY b.nama_brg, a.is_supp_utama DESC";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function barang_supplier_detail ($bsid) /*{{{*/
    {
        if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*, b.kode_brg, b.nama_brg
                FROM barang_supplier a
                JOIN m_barang b ON b.mbid = a.mbid
                WHERE a.bsid = ?";
        $rs = DB::Execute($sql, array($bsid));

        return $rs;
    } /*}}}*/

    public static function cari_barang () /*{{{*/
    {
        $q = get_var('q');

        if ($q) $addSql .= " AND LOWER(kode_brg || nama_brg) LIKE '%$q%'";

        $sql = "SELECT (kode_brg || ' - ' || nama_brg) AS barang, mbid
                    , kode_brg, nama_brg, kode_satuan, hna
                FROM m_barang
                WHERE 1 = 1 $addsql
                ORDER BY nama_brg";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_data () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bsid = get_var('bsid', 0);
        $suppid = get_var('suppid', 0);
        $mbid = get_var('mbid', 0);
        $kode_satuan = get_var('kode_satuan');
        $harga = get_var('harga');
        $disc = get_var('disc');
        $keterangan = get_var('keterangan');
        $is_supp_utama = get_var('is_supp_utama', 'f');
        $userid = Auth::user()->pid;

        $sql = "SELECT * FROM barang_supplier WHERE suppid = ? AND mbid = ? AND bsid <> ?";
        $rs_check = DB::Execute($sql, [$suppid, $mbid, $bsid]);

        if (!$rs_check->EOF) return 'Sudah ada data yang sama';

        DB::BeginTrans();

        $record = array();
        $record['suppid']           = $suppid;
        $record['mbid']             = $mbid;
        $record['kode_satuan']      = $kode_satuan;
        $record['harga']            = $harga;
        $record['disc']             = $disc;
        $record['keterangan']       = $keterangan;
        $record['is_supp_utama']    = $is_supp_utama;

        $sql = "SELECT * FROM barang_supplier WHERE suppid = ? AND mbid = ?";
        $rs = DB::Execute($sql, [$suppid, $mbid]);

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);
        }
        else
        {
            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);
        }

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

    public static function delete_data ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "DELETE FROM barang_supplier WHERE bsid = ?";
        $ok = DB::Execute($sql, array($myid));

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