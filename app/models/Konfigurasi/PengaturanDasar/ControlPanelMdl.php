<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class ControlPanelMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function group_configs () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT config_name, cgid FROM configs_group ORDER BY config_name";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $s_cgid = $data['s_cgid'];
        $s_deskripsi = strtolower(trim($data['s_deskripsi']));
        $s_data = strtolower(trim($data['s_data']));

        if ($s_cgid) $addsql .= " AND a.cgid = ".$s_cgid;

        if ($s_deskripsi) $addsql .= " AND LOWER(a.keterangan) ILIKE '%$s_deskripsi%'";

        if ($s_data) $addsql .= " AND a.data = '$s_data'";

        $sql = "SELECT a.*, b.config_name, c.nama_lengkap AS user
                FROM configs a
                INNER JOIN configs_group b ON b.cgid = a.cgid
                LEFT JOIN person c ON a.modify_by = c.pid
                WHERE a.is_show = 't' $addsql
                ORDER BY b.urutan, a.confname";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function configs_detail ($cid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*, b.config_name
                FROM configs a, configs_group b
                WHERE b.cgid = a.cgid AND a.is_show = 't' AND a.cid = ?";
        $rs = DB::Execute($sql, array($cid));

        return $rs;
    } /*}}}*/

    public static function save_configs () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $cid = get_var('e_cid', 0);
        $keterangan = get_var('e_keterangan');
        $data = get_var('e_data');
        $pid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['keterangan']   = $keterangan;
        $record['data']         = $data;
        $record['modify_by']    = $pid;
        $record['modify_time']  = 'NOW()';

        $sql = "SELECT * FROM configs WHERE cid = ?";
        $rs = DB::Execute($sql, array($cid));
        $sqlu = DB::UpdateSQL($rs, $record);
        $ok = DB::Execute($sqlu);

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