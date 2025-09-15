<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class UnlockCoaMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT * FROM m_coagroup ORDER BY coagid";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function group_coa_detail ($coagid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_coagroup WHERE coagid = ?";
        $rs = DB::Execute($sql, array($coagid));

        return $rs;
    } /*}}}*/

    public static function save_unlock_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $coagid = get_var('coagid', 0);
        $start_period = get_var('start_period', date('d-m-Y'));
        $end_period = get_var('end_period', date('d-m-Y'));
        $pid = Auth::user()->pid;

        DB::BeginTrans();

        $sqlu = "UPDATE m_coagroup
                SET is_lock = 'f',
                    start_period = '$start_period',
                    end_period = '$end_period',
                    modify_by = '$pid',
                    modify_time = 'NOW()'
                WHERE coagid = ?";
        $ok = DB::Execute($sqlu, array($coagid));

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