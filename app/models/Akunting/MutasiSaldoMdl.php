<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class MutasiSaldoMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $mutasi_speriod = $data['mutasi_speriod'];
        $mutasi_eperiod = $data['mutasi_eperiod'];
        $bank_from = $data['bank_from'];
        $bank_to = $data['bank_to'];

        if ($bank_from) $addsql .= " AND a.bank_from = ".$bank_from;

        if ($bank_to) $addsql .= " AND a.bank_to = ".$bank_to;

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.msid, a.mutasi_date, a.mutasi_code
                    , b.bank_nama AS bank_from, c.bank_nama AS bank_to
                    , a.amount, a.keterangan, d.nama_lengkap AS useri
                FROM mutasi_saldo a
                JOIN m_bank b ON b.bank_id = a.bank_from
                JOIN m_bank c ON c.bank_id = a.bank_to
                JOIN person d ON d.pid = a.create_by
                WHERE DATE(mutasi_date) BETWEEN DATE('$mutasi_speriod') AND DATE('$mutasi_eperiod')
                    $addsql
                ORDER BY a.mutasi_date";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_mutasi ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.msid, a.mutasi_date, a.mutasi_code
                    , b.bank_nama AS bank_from, c.bank_nama AS bank_to
                    , a.amount, a.keterangan, d.nama_lengkap AS useri
                FROM mutasi_saldo a
                JOIN m_bank b ON b.bank_id = a.bank_from
                JOIN m_bank c ON c.bank_id = a.bank_to
                JOIN person d ON d.pid = a.create_by
                WHERE a.msid = ? AND a.bid = ?";
        $res = DB::Execute($sql, [$myid, $bid]);

        return $res;
    } /*}}}*/

    public static function cek_saldo ($mybank) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;
        $mydate = get_var('mydate');

        $sql = "SELECT SUM(a.debet - a.credit)
                FROM general_ledger_d a
                INNER JOIN general_ledger b ON b.glid = a.glid
                INNER JOIN m_bank c ON c.default_coaid = a.coaid
                WHERE c.bank_id = ? AND b.gldate <= ? AND b.bid = ?";
        $res = DB::GetOne($sql, [$mybank, $mydate, $bid]);

        return $res;
    } /*}}}*/

    public static function save_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $msid = get_var('msid', 0);
        $mutasi_date = get_var('mutasi_date');
        $bank_from = get_var('bank_from');
        $bank_to = get_var('bank_to');
        $amount = get_var('amount');
        $keterangan = get_var('keterangan');
        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['mutasi_date']  = $mutasi_date;
        $record['bank_from']    = $bank_from;
        $record['bank_to']      = $bank_to;
        $record['amount']       = $amount;
        $record['keterangan']   = $keterangan;
        $record['is_posted']    = 't';
        $record['create_by']    = $record['modify_by'] = $pid;
        $record['bid']          = $bid;

        $sql = "SELECT * FROM mutasi_saldo WHERE msid = ?";
        $rs = DB::Execute($sql, array($msid));
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

    public static function delete_trans ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        DB::BeginTrans();

        $bid = Auth::user()->branch->bid;

        $sql = "DELETE FROM mutasi_saldo WHERE msid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

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