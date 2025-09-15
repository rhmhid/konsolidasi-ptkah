<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PosplMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT 'REKAP' AS name, 1 AS id UNION ALL
                SELECT 'DETAIL' AS name, 2 AS id
                ORDER BY id";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function jenis_pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT 'PENDAPATAN' AS name, 4 AS id UNION ALL
                SELECT 'BEBAN' AS name, 5 AS id
                ORDER BY id";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";

        $sql = "SELECT a.*, b.nama_pos AS parent_pos
                FROM pos_pl a
                LEFT JOIN pos_pl b ON a.parent_pplid = b.pplid
                WHERE 1 = 1 $addsql
                ORDER BY a.urutan";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function parent_post ($pplid = 0) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.nama_pos, a.pplid
                FROM pos_pl a
                WHERE a.pplid <> ?
                ORDER BY a.urutan";
        $rs = DB::Execute($sql, array($pplid));

        return $rs;
    } /*}}}*/

    public static function posisi_detail ($pplid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_pl WHERE pplid = ?";
        $rs = DB::Execute($sql, array($pplid));

        return $rs;
    } /*}}}*/

    public static function save_posisi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pplid = get_var('pplid', 0);
        $jenis_pos = get_var('jenis_pos');
        $urutan = get_var('urutan');
        $kode_pos = get_var('kode_pos');
        $nama_pos = get_var('nama_pos');
        $parent_pplid = get_var('parent_pplid', NULL);
        $level = get_var('level');
        $sum_total = get_var('sum_total', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['jenis_pos']    = $jenis_pos;
        $record['urutan']       = $urutan;
        $record['kode_pos']     = $kode_pos;
        $record['nama_pos']     = $nama_pos;
        $record['parent_pplid']  = $parent_pplid;
        $record['level']        = $level;
        $record['sum_total']    = $sum_total;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM pos_pl WHERE pplid = ?";
        $rs = DB::Execute($sql, array($pplid));

        if ($rs->EOF)
        {
            $record['create_by'] = $record['modify_by'] = $userid;

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
}
?>