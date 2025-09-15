<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PeriodeAkuntingMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT * FROM periode_akunting ORDER BY pbegin, pend, paid";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function save_periode_akunting () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $paid = get_var('paid', 0);
        $pbegin = get_var('pbegin', date('Y-m-d'));
        $pend = get_var('pend', date('Y-m-d'));
        $description = get_var('description');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM periode_akunting WHERE paid = ?";
        $rs = DB::Execute($sql, array($paid));

        $record = array();
        $record['pbegin']       = date('Y-m-d', strtotime($pbegin));
        $record['pend']         = date('Y-m-d', strtotime($pend));
        $record['description']  = $description;

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
}
?>