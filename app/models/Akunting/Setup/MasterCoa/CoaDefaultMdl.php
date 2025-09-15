<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class CoaDefaultMdl extends DB
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
        $sctype = $data['sctype'];

        if ($sctype) $addsql .= " AND a.sctype = ".$sctype;

        $sql = "SELECT a.scid, (c.coacode || ' - ' || c.coaname) AS coa,
                    b.nama_lengkap AS create_by, a.create_time, a.is_aktif
                FROM setup_coa a
                JOIN person b ON b.pid = a.create_by
                LEFT JOIN m_coa c ON a.coaid = c.coaid
                WHERE 1 = 1 $addsql
                ORDER BY coa";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function setup_coa ($sctype) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (b.coacode || ' - ' || b.coaname) AS coa, b.coaid
                FROM m_coa b
                LEFT JOIN setup_coa a ON b.coaid = a.coaid AND a.sctype = ?
                WHERE b.is_valid = 't' AND b.allow_post = 't' AND a.coaid ISNULL
                    AND b.coaid NOT IN (SELECT coaid FROM default_coa WHERE default_code IN ('RETAINEDEARNING_ACCT', 'INCOMESUMMARY_ACCT'))
                ORDER BY b.coacode";
        $rs = DB::Execute($sql, array($sctype));

        return $rs;
    } /*}}}*/

    public static function save_coa_default ($sctype) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $coaid = get_var('sc_coaid', Null);
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['sctype']       = $sctype;
        $record['coaid']        = $coaid;
        $record['create_by']    = $record['modify_by'] = $userid;

        $sql = "SELECT * FROM setup_coa WHERE 1 = 2";
        $rs = DB::Execute($sql);
        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

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

    public static function update_coa_default ($sctype) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        DB::BeginTrans();

        $scid = get_var('scid', 0);
        $userid = Auth::user()->pid;

        $sqlu = "UPDATE setup_coa
                SET
                    is_aktif    = (CASE WHEN is_aktif = 't' THEN FALSE ELSE TRUE END),
                    modify_by   = $userid,
                    modify_time = 'NOW()'
                WHERE sctype = ? AND scid = ?";
        $ok = DB::Execute($sqlu, array($sctype, $scid));

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            
            $errmsg = 'Debug Sql Mode';
            return $errmsg;
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