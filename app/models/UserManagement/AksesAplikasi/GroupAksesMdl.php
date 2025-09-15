<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class GroupAksesMdl extends DB
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
        $kode_nama_group = strtolower(trim($data['kode_nama_group']));

        if ($kode_nama_group) $addsql .= " AND LOWER(role_kode || role_name) ILIKE '%$kode_nama_group%'";

        $sql = "SELECT * FROM role_group WHERE 1 = 1 $addsql ORDER BY role_name";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function group_akses_detail ($rgid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM role_group WHERE rgid = ?";
        $rs = DB::Execute($sql, array($rgid));

        return $rs;
    } /*}}}*/

    public static function list_module () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*
                FROM menus a
                WHERE a.is_aktif = 't' AND a.is_display = 't' AND a.super_admin = 'f'
                ORDER BY a.level, a.urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        if ($jenis == 'gakses')
        {
            $sql = "SELECT * FROM role_group WHERE LOWER(role_kode) = LOWER(?)";

            $pkey = 'rgid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$pkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_group_akses () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $rgid = get_var('rgid', 0);
        $role_kode = get_var('role_kode');
        $role_name = get_var('role_name');
        $is_aktif = get_var('is_aktif', 'f');
        $mid = get_var('mid');
        $userid = Auth::user()->pid;

        $role_mid = '';
        if (is_array($mid)) $role_mid .= implode(",", $mid);

        DB::BeginTrans();

        $record = array();
        $record['role_kode']    = $role_kode;
        $record['role_name']    = $role_name;
        $record['role_mid']     = $role_mid;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM role_group WHERE rgid = ?";
        $rs = DB::Execute($sql, array($rgid));

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