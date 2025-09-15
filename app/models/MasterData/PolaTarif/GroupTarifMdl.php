<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class GroupTarifMdl extends DB
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
        $s_kode_nama = strtolower(trim($data['s_kode_nama']));

        if ($s_kode_nama) $addsql .= " AND LOWER(a.group_code || a.group_name) ILIKE ('%$s_kode_nama%')";

        $sql = "SELECT a.*
                FROM group_tarif a
                WHERE 1 = 1 $addsql
                ORDER BY a.gtid";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function group_tarif_detail ($gtid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM group_tarif WHERE gtid = ?";
        $rs = DB::Execute($sql, array($gtid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM group_tarif WHERE LOWER(group_code) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['gtid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_group_tarif () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $gtid = get_var('gtid', 0);
        $group_code = get_var('group_code');
        $group_name = get_var('group_name');
        $keterangan = get_var('keterangan');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['bank_type']    = $bank_type;
        $record['group_code']   = $group_code;
        $record['group_name']   = $group_name;
        $record['keterangan']   = $keterangan;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM group_tarif WHERE gtid = ?";
        $rs = DB::Execute($sql, array($gtid));

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
}
?>