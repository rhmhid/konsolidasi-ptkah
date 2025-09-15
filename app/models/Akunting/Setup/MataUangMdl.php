<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class MataUangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $s_kode_nama_curr = strtolower(trim($data['s_kode_nama_curr']));
        $addsql = "";

        if ($s_kode_nama_curr) $addsql .= " AND LOWER(curr_code || curr_name) ILIKE ('%$s_kode_nama_curr%')";

        $sql = "SELECT * FROM currency WHERE 1 = 1 $addsql ORDER BY curr_code";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function currency_detail ($cid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM currency WHERE cid = ?";
        $rs = DB::Execute($sql, array($cid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        if ($jenis == 'curr')
        {
            $sql = "SELECT * FROM currency WHERE LOWER(curr_code) = LOWER(?)";

            $pkey = 'cid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$pkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_currency () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $cid = get_var('cid', 0);
        $curr_code = get_var('curr_code');
        $curr_name = get_var('curr_name');
        $curr_desc = get_var('curr_desc');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM currency WHERE cid = ?";
        $rs = DB::Execute($sql, array($cid));

        $record = array();
        $record['curr_code']    = $curr_code;
        $record['curr_name']    = $curr_name;
        $record['curr_desc']    = $curr_desc;
        $record['is_aktif']     = $is_aktif;

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

    public static function list_rates ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*, b.nama_lengkap AS create_by
                FROM currency_rate a
                JOIN person b ON b.pid = a.create_by
                WHERE a.cid = ?
                ORDER BY a.curr_start DESC, a.crid DESC";
        $rs = DB::Execute($sql, array($data['cid']));

        return $rs;
    } /*}}}*/

    public static function save_currency_rate () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $cid = get_var('cid', 0);
        $curr_start = get_var('curr_start', date('d-m-Y H:i'));
        $curr_rate = get_var('curr_rate');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['cid']          = $cid;
        $record['curr_start']   = $curr_start;
        $record['curr_rate']    = $curr_rate;
        $record['is_aktif']     = $is_aktif;
        $record['create_by']    = $record['modify_by'] = $userid;

        $sql = "SELECT * FROM currency_rate WHERE 1 = 2";
        $rs = DB::Execute($sql);
        $newsql = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($newsql);

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