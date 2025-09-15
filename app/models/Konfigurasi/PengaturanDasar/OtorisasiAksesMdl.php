<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class OtorisasiAksesMdl extends DB
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
        $otogid = $data['s_otogid'];
        $nama_user = strtolower(trim($data['s_nama_user']));

        if ($otogid) $addsql .= " AND a.otogid = ".$otogid;

        if ($nama_user) $addsql .= " AND LOWER(c.nama_lengkap) ILIKE '%$nama_user%'";

        $sql = "SELECT a.otoid, b.description, c.nama_lengkap, d.nama_lengkap AS useri, a.create_time
                FROM otorisasi a
                INNER JOIN otorisasi_group b ON b.otogid = a.otogid
                INNER JOIN person c ON c.pid = a.pid
                INNER JOIN person d ON d.pid = a.create_by
                WHERE a.otoid != 0 $addsql
                ORDER BY b.description, c.nama_lengkap";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function cari_user () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $key = get_var("q");
        $otogid = get_var("otogid");
        $addsql = '';

        if ($otogid != '') $addsql .= " WHERE c.otogid = ".$otogid;

        $sql = "SELECT (a.username || ' - ' || b.nama_lengkap) AS text, b.pid AS id
                FROM app_users a
                INNER JOIN person b ON b.pid = a.pid
                WHERE a.pid > 0 AND LOWER(a.username || b.nrp || b.nama_lengkap) ILIKE LOWER('%$key%')
                    AND a.pid NOT IN (SELECT c.pid FROM otorisasi c $addsql)
                ORDER BY text";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_otorisasi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = get_var('i_pid');
        $otogid = get_var('otogid_val');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['otogid']   = $otogid;
        $record['pid']      = $pid;

        $sql = "SELECT * FROM otorisasi WHERE otogid = ? AND pid = ?";
        $rs = DB::Execute($sql, array($otogid, $pid));

        if ($rs->EOF)
        {
            $record['create_by'] = $record['modify_by'] = $userid;

            $newsql = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($newsql);
        }
        else
        {
            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $newsql = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($newsql);
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

    public static function delete_otorisasi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $otoid = get_var('otoid');

        DB::BeginTrans();

        $sql = "SELECT COUNT(otolid) FROM otorisasi_logs WHERE otoid = ?";
        $cek_data = DB::GetOne($sql, array($otoid));

        if ($cek_data > 0)
        {
            $errmsg = "Data tidak bisa dihapus, sudah digunakan.";

            DB::RollbackTrans();
            return $errmsg;
        }

        $sql = "DELETE FROM otorisasi WHERE otoid = ?";
        $ok = DB::Execute($sql, array($otoid));

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