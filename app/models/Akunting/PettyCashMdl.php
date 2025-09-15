<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PettyCashMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list_type ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $coaid = $data['coaid'];
        $type_trans = $data['type_trans'];
        $keterangan = strtolower(trim($data['keterangan']));

        if ($coaid) $addsql .= " AND a.coaid = ".$coaid;

        if ($type_trans) $addsql .= " AND a.type_trans = ".$type_trans;

        if ($keterangan) $addsql .= " AND LOWER(a.keterangan) ILIKE '%$keterangan%'";

        $sql = "SELECT a.*, (b.coacode || ' - ' || b.coaname) AS coa
                FROM petty_cash_type a
                JOIN m_coa b ON b.coaid = a.coaid
                WHERE 1 = 1 $addsql
                ORDER BY a.keterangan";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function type_detail ($pctid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM petty_cash_type WHERE pctid = ?";
        $rs = DB::Execute($sql, array($pctid));

        return $rs;
    } /*}}}*/

    public static function data_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' - ' || a.coaname) AS coa, a.coaid
                FROM m_coa a
                WHERE a.allow_post = 't' AND a.is_valid = 't' AND a.is_petty_cash = 't'
                ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_type () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pctid = get_var('pctid', 0);
        $keterangan = get_var('keterangan');
        $coaid = get_var('coaid', NULL);
        $type_trans = get_var('type_trans');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['keterangan']   = $keterangan;
        $record['coaid']        = $coaid;
        $record['type_trans']   = $type_trans;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM petty_cash_type WHERE pctid = ?";
        $rs = DB::Execute($sql, array($pctid));

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

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $bank_id = $data['bank_id'];
        $pccode = strtolower(trim($data['pccode']));
        $keterangan = strtolower(trim($data['keterangan']));

        if ($bank_id) $addsql .= " AND a.bank_id = ".$bank_id;

        if ($pccode) $addsql .= " AND a.pccode = '$pccode'";

        if ($keterangan) $addsql .= " AND LOWER(a.keterangan) ILIKE '%$keterangan%'";

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.pcid, a.pcdate, a.pccode, b.bank_nama AS cash_book
                    , a.keterangan, c.nama_lengkap AS useri, d.glid
                FROM petty_cash a
                INNER JOIN m_bank b ON b.bank_id = a.bank_id
                JOIN person c ON c.pid = a.create_by
                LEFT JOIN general_ledger d ON a.pcid = d.reff_id AND d.jtid = 30
                WHERE DATE(a.pcdate) BETWEEN '$sdate' AND '$edate' $addsql
                ORDER BY a.pcdate DESC, a.pccode DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_trans ($pcid)
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT b.pcid, b.pcdate, b.pccode, b.bank_id, d.bank_nama AS cash_book
                    , b.keterangan, a.pcdid, a.pctid, c.keterangan AS ket_trans, f.coatid
                    , a.type_trans, a.notes, a.debet, a.credit, a.pccid
                    , e.nama_lengkap AS petugas, (g.pcccode || ' - ' || g.pccname) AS cost_center
                FROM petty_cash_d a
                INNER JOIN petty_cash b ON b.pcid = a.pcid
                INNER JOIN petty_cash_type c ON c.pctid = a.pctid
                INNER JOIN m_bank d ON d.bank_id = b.bank_id
                INNER JOIN person e ON e.pid = b.create_by
                INNER JOIN m_coa f ON f.coaid = c.coaid
                LEFT JOIN profit_cost_center g ON a.pccid = g.pccid
                WHERE b.pcid = ? AND b.bid = ?
                ORDER BY a.pcdid";
        $rs = DB::Execute($sql, [$pcid, $bid]);

        return $rs;
    }

    public static function data_cash_book () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT bank_nama, bank_id FROM m_bank WHERE is_aktif = 't' AND is_petty_cash = 't' ORDER BY LOWER(bank_nama)";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $key = get_var("q");

        $sql = "SELECT a.keterangan, a.pctid, a.type_trans, b.coatid
                FROM petty_cash_type a
                JOIN m_coa b ON b.coaid = a.coaid
                WHERE a.is_aktif = 't' AND LOWER(a.keterangan) ILIKE LOWER('%$key%')
                ORDER BY a.keterangan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function check_saldo ($mybank) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;
        $pcdate = get_var('pcdate');

        $sql = "SELECT SUM(a.debet - a.credit)
                FROM general_ledger_d a
                JOIN general_ledger b ON b.glid = a.glid
                WHERE b.gldate < ? AND b.is_posted = 't'
                    AND a.coaid = (SELECT default_coaid FROM m_bank WHERE bank_id = ?)
                    AND b.bid = ?";
        $cash_book_amount = DB::GetOne($sql, [$pcdate, $mybank, $bid]);

        return $cash_book_amount;
    } /*}}}*/

    public static function save () /*{{{*/
    {
        //         if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pcid = get_var('pcid', 0);
        // $pcdate = get_var('pcdate', date('d-m-Y H:i'));
        $pcdate = get_var('pcdate', date('d-m-Y'));
        $bank_id = get_var('bank_id');
        $keterangan = get_var('keterangan');

        $pcdid = get_var('pcdid');
        $pctid = get_var('pctid');
        $type_trans = get_var('type_trans');
        $notes = get_var('notes');
        $debet = get_var('debet');
        $credit = get_var('credit');
        $pccid = get_var('pccid');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT * FROM petty_cash WHERE pcid = ?";
        $rs = DB::Execute($sql, array($pcid));

        $record = array();
        $record['pcdate']       = $pcdate;
        $record['bank_id']      = $bank_id;
        $record['keterangan']   = $keterangan;
        $record['bid']          = $bid;

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $pid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            $pcid = DB::GetOne("SELECT CURRVAL('petty_cash_pcid_seq') AS code");
        }
        else
        {
            $pcid = $rs->fields['pcid'];

            $sqlu = "UPDATE petty_cash SET is_posted = 'f' WHERE pcid = ?";
            $ok = DB::Execute($sqlu, array($pcid));

            $record['modify_by']    = $pid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqlu);
        }

        if (is_array($pcdid))
        {
            $arr_pcdid = "";

            foreach ($pcdid as $k => $v)
            {

                $sql = "SELECT * FROM petty_cash_d WHERE pcid = ? AND pctid = ? AND pcdid = ?";
                $rss = DB::Execute($sql, array($pcid, $pctid[$k],$v));

                $recordd = array();
                $recordd['pctid']       = $pctid[$k];
                $recordd['type_trans']  = $type_trans[$k];
                $recordd['notes']       = $notes[$k];
                $recordd['debet']       = $debet[$k];
                $recordd['credit']      = $credit[$k];
                $recordd['pccid']       = intval($pccid[$k]) == 0 ? NULL : $pccid[$k];
                $recordd['bid']         = $bid;

                if ($rss->EOF)
                {
                    $recordd['pcid']        = $pcid;
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $pcdid = DB::GetOne("SELECT CURRVAL('petty_cash_d_pcdid_seq') AS code");
                }
                else
                {
                    $pcdid = $rss->fields['pcdid'];

                    $recordd['modify_by']    = $pid;
                    $recordd['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_pcdid .= $pcdid.",";
            }

            $arr_pcdid .= "0";

            $sqld = "DELETE FROM petty_cash_d WHERE pcid = ? AND pcdid NOT IN ($arr_pcdid)";
            if ($ok) $ok = DB::Execute($sqld, array($pcid));
        }

        $sql = "UPDATE petty_cash SET is_posted = 't' WHERE pcid = ?";
        if ($ok) $ok = DB::Execute($sql, array($pcid));

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            die('debug');
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

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "UPDATE petty_cash SET is_posted = 'f' WHERE pcid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM petty_cash_d WHERE pcid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM petty_cash WHERE pcid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

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