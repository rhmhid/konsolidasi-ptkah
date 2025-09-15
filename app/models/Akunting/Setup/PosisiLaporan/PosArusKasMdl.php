<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PosArusKasMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function jenis_pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT 'DIRECT' AS name, 1 AS id UNION ALL
                SELECT 'INDIRECT' AS name, 2 AS id
                ORDER BY id";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $s_jenis_pos = $data['s_jenis_pos'];

        if ($s_jenis_pos)
            $addsql .= " AND a.jenis_pos = ".$s_jenis_pos;

        $sql = "SELECT a.*, b.nama_pos AS parent_pos
                FROM pos_cashflow a
                LEFT JOIN pos_cashflow b ON a.parent_pcfid = b.pcfid
                WHERE 1 = 1 $addsql
                ORDER BY a.urutan";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function parent_post ($jenis_pos, $pcfid = 0) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.nama_pos, a.pcfid
                FROM pos_cashflow a
                WHERE a.jenis_pos = ? AND a.pcfid <> ?
                ORDER BY a.urutan";
        $rs = DB::Execute($sql, array($jenis_pos, $pcfid));

        return $rs;
    } /*}}}*/

    public static function posisi_detail ($pcfid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_cashflow WHERE pcfid = ?";
        $rs = DB::Execute($sql, array($pcfid));

        return $rs;
    } /*}}}*/

    public static function save_posisi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pcfid = get_var('pcfid', 0);
        $jenis_pos = get_var('jenis_pos');
        $urutan = get_var('urutan');
        $kode_pos = get_var('kode_pos');
        $nama_pos = get_var('nama_pos');
        $parent_pcfid = get_var('parent_pcfid', NULL);
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
        $record['parent_pcfid'] = $parent_pcfid;
        $record['level']        = $level;
        $record['sum_total']    = $sum_total;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM pos_cashflow WHERE pcfid = ?";
        $rs = DB::Execute($sql, array($pcfid));

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