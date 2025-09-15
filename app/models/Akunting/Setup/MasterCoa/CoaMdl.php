<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class CoaMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list_coa_type () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (typecode || '. ' || typedesc) AS name, coatid FROM m_coatype ORDER BY coatid";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list ($coatid, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT a.coatid, a.coaid, (a.coacode || ' ' || a.coaname) AS coa, a.level
                    , a.allow_post, a.default_debet, a.is_valid, b.coatype, c.coa_group
                    , d.nama_pos AS pos_na, e.nama_pos AS pos_pl, f.nama_pos AS pos_cf
                FROM m_coa a
                INNER JOIN m_coatype b ON b.coatid = a.coatid
                INNER JOIN m_coagroup c ON c.coagid = a.coagid
                LEFT JOIN pos_neraca d ON a.pnid = d.pnid
                LEFT JOIN pos_pl e ON a.pplid = e.pplid
                LEFT JOIN pos_cashflow f ON a.pcfdid = f.pcfid
                WHERE a.coatid = ?
                ORDER BY a.coacode";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx, array($coatid));

        return $rs;
    } /*}}}*/

    public static function group_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT coa_group, coagid FROM m_coagroup ORDER BY coagid";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function parent_coa ($coatid, $coaid = 0) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' - ' || a.coaname) AS coa, a.coaid
                FROM m_coa a
                WHERE a.is_valid = 't' AND a.coatid = ? AND a.allow_post = 'f' AND a.coaid <> ?
                    AND a.coaid NOT IN (SELECT coaid FROM default_coa WHERE default_code IN ('RETAINEDEARNING_ACCT', 'INCOMESUMMARY_ACCT') AND coaid NOTNULL)
                ORDER BY coa";
        $rs = DB::Execute($sql, array($coatid, $coaid));

        return $rs;
    } /*}}}*/

    public static function coa_detail ($coaid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_coa WHERE coaid = ?";
        $rs = DB::Execute($sql, array($coaid));

        return $rs;
    } /*}}}*/

    public static function save_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $coatid = get_var('coatid', NULL);
        $coaid = get_var('coaid', 0);
        $coagid = get_var('coagid', NULL);
        $default_debet = get_var('default_debet', NULL);
        $allow_post = get_var('allow_post', NULL);
        $parent_coaid = get_var('parent_coaid', NULL);
        $coacode = get_var('coacode');
        $coaname = get_var('coaname');
        $pnid = get_var('pnid', NULL);
        $pplid = get_var('pplid', NULL);
        $pcfdid = get_var('pcfdid', NULL);
        // $period_reset = get_var('period_reset', 'f');
        $is_petty_cash = get_var('is_petty_cash', 'f');
        $is_manual_journal = get_var('is_manual_journal', 'f');
        $is_valid = get_var('is_valid', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM m_coa WHERE coaid = ?";
        $rs = DB::Execute($sql, array($coaid));

        $parent_coaid = $parent_coaid;
        if ($parent_coaid == '')
        {
            $parent_coaid = NULL;
            $level = NULL;
        }
        else
        {
            $parent_coaid = $parent_coaid;
            $level = DB::GetOne("SELECT COALESCE(level, 0) + 1 FROM m_coa WHERE coaid = ?", array($parent_coaid));
        }

        $record = array();
        $record['coacode']              = $coacode;
        $record['coaname']              = $coaname;
        $record['parent_coaid']         = $parent_coaid;
        $record['level']                = $level;
        $record['coatid']               = $coatid;
        $record['default_debet']        = $default_debet;
        $record['period_reset']         = $coatid == 4 || $coatid == 5 ? 't' : 'f';
        $record['allow_post']           = $allow_post;
        $record['is_valid']             = $is_valid;
        $record['is_petty_cash']        = $is_petty_cash;
        $record['is_manual_journal']    = $is_manual_journal;
        $record['coagid']               = $coagid;
        $record['pnid']                 = $pnid;
        $record['pplid']                = $pplid;
        $record['pcfdid']               = $pcfdid;

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