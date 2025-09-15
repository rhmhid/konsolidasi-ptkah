<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PeriodeTarifMdl extends DB
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

        if ($s_kode_nama) $addsql .= " AND LOWER(a.periode_code || a.periode_name) ILIKE LOWER('%$s_kode_nama%')";

        $sql = "SELECT a.*
                FROM periode_tarif a
                WHERE 1 = 1 $addsql
                ORDER BY a.periode_start, a.periode_id";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function periode_tarif_detail ($periode_id) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM periode_tarif WHERE periode_id = ?";
        $rs = DB::Execute($sql, array($periode_id));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM periode_tarif WHERE LOWER(periode_code) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['periode_id'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_periode_tarif () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $periode_id = get_var('periode_id', 0);
        $periode_code = get_var('periode_code');
        $periode_name = get_var('periode_name');
        $periode_start = get_var('periode_start');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['periode_code']     = $periode_code;
        $record['periode_name']     = $periode_name;
        $record['periode_start']    = $periode_start;
        $record['is_active']        = $is_aktif;

        $sql = "SELECT * FROM periode_tarif WHERE periode_id = ?";
        $rs = DB::Execute($sql, array($periode_id));

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