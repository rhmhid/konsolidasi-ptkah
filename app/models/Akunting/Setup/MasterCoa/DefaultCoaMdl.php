<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class DefaultCoaMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT a.*, b.coacode, b.coaname
                FROM default_coa a
                LEFT JOIN m_coa b ON a.coaid = b.coaid
                WHERE a.is_aktif = 't'
                ORDER BY dcid";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function default_coa_detail ($dcid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM default_coa WHERE dcid = ?";
        $rs = DB::Execute($sql, array($dcid));

        return $rs;
    } /*}}}*/

    public static function list_coa () /*{{{*/
    {
        $sql = "SELECT (a.coacode || ' - ' || a.coaname) AS coa, a.coaid
                FROM m_coa a
                WHERE a.is_valid = 't' AND a.allow_post = 't'
                ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function update_default_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $dcid = get_var('dcid', 0);
        $default_code = get_var('default_code');
        $default_desc = get_var('default_desc');
        $coaid = get_var('coaid', Null);
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['coaid']        = $coaid;
        $record['modify_by']    = $userid;
        $record['modify_time']  = 'NOW()';

        $sql = "SELECT * FROM default_coa WHERE dcid = ?";
        $rs = DB::Execute($sql, array($dcid));
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