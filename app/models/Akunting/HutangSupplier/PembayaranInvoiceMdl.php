<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PembayaranInvoiceMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $suppid = $data['suppid'];
        $paycode = strtolower(trim($data['paycode']));

        if ($suppid) $addsql .= " AND a.suppid = ".$suppid;

        if ($paycode) $addsql .= " AND LOWER(a.paycode) = '$paycode'";

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.appid, a.paydate, a.paycode, b.nama_supp, a.totpay
                    , ps.nama_lengkap AS useri, gl.glid
                FROM ap_payment a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN general_ledger gl ON a.appid = gl.reff_id AND gl.jtid = 21
                INNER JOIN person ps ON ps.pid = a.create_by
                WHERE DATE(a.paydate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY a.paydate DESC, a.appid DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_trans ($appid) /*}}}*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT b.appdid, a.appid, a.paydate, a.paycode, a.suppid, c.nama_supp
                    , c.bank, c.no_rek, a.bank_id, a.tgl_bayar, a.keterangan
                    , b.apsid, g.apcode, g.apdate, g.no_invoice, g.no_faktur_pajak
                    , a.cara_bayar, a.potongan, a.pembulatan, a.other_cost, a.totpay
                    , g.duedate, g.keterangan AS ket_ap, g.amount AS nominal_hutang
                    , b.nominal AS nominal_payment, a.no_bayar, d.nama_lengkap AS petugas
                    , e.glid, format_glcode(e.gldate, f.doc_code, e.gldoc, e.glid) AS doc_no
                FROM ap_payment a
                INNER JOIN ap_payment_d b ON a.appid = b.appid
                INNER JOIN m_supplier c ON c.suppid = a.suppid
                INNER JOIN person d ON d.pid = a.create_by
                LEFT JOIN general_ledger e ON a.appid = e.reff_id AND e.jtid = 21
                LEFT JOIN journal_type f ON e.jtid = f.jtid
                LEFT JOIN ap_supplier g ON b.apsid = g.apsid
                INNER JOIN m_bank h ON h.bank_id = a.bank_id
                WHERE a.appid = ? AND a.bid = ?
                ORDER BY b.apsid";
        $rs = DB::Execute($sql, [$appid, $bid]);

        return $rs;
    } /*}}}*/

    public static function detail_addless ($appid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.appaid, a.coaid, (b.coacode || ' - ' || b.coaname) AS coa
                    , a.debet, a.credit, a.ket_addless, a.pccid
                FROM ap_payment_addless a
                INNER JOIN m_coa b ON b.coaid = a.coaid
                WHERE a.appid = ?";
        $rs = DB::Execute($sql, [$appid]);

        return $rs;
    } /*}}}*/

    public static function list_outstanding_ap ($suppid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.apsid, a.apcode, a.apdate, a.no_invoice, a.no_faktur_pajak
                    ,a.duedate, a.amount AS nominal_hutang, a.keterangan
                    , (a.amount - COALESCE(c.payment, 0)) AS sisa_hutang
                FROM ap_supplier a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN (
                    SELECT x.apsid, SUM(x.nominal) AS payment
                    FROM ap_payment_d x
                    GROUP BY x.apsid
                ) c ON a.apsid = c.apsid
                WHERE a.suppid = ? AND a.bid = ? AND (a.amount - COALESCE(c.payment, 0)) > 0
                ORDER BY a.apdate, a.apsid DESC";
        $rs = DB::Execute($sql, [$suppid, $bid]);

        return $rs;
    } /*}}}*/

    public static function data_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' ' || a.coaname) AS coa, a.coaid, a.coatid
                    , a.coacode, a.coaname
                FROM m_coa a
                WHERE a.allow_post = 't' AND a.is_valid = 't' AND a.is_manual_journal = 't'
                    AND a.coatid IN (4, 5)
                ORDER BY a.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $appid = get_var('appid', 0);
        $paydate = get_var('paydate');
        $tgl_bayar = get_var('tgl_bayar');
        $suppid = get_var('suppid', NULL);
        $bank_id = get_var('bank_id', NULL);
        $cara_bayar = get_var('cara_bayar', NULL);
        $no_bayar = get_var('no_bayar');
        $keterangan = get_var('keterangan');
        $potongan = get_var('potongan', 0);
        $pembulatan = get_var('pembulatan', 0);
        $other_cost = get_var('other_cost', 0);
        $totpay = get_var('totpay', 0);

        // Detail Inv
        $appdid = get_var('appdid');
        $apsid = get_var('apsid');
        $apcode = get_var('apcode');
        $pay = get_var('pay');

        // Detail Add/Less
        $appaid = get_var('appaid');
        $coaid = get_var('coaid');
        $debet = get_var('debet');
        $credit = get_var('credit');
        $ket_addless = get_var('ket_addless');
        $pccid = get_var('pccid');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['paydate']      = $paydate;
        $record['suppid']       = $suppid;
        $record['bank_id']      = $bank_id;
        $record['cara_bayar']   = $cara_bayar;
        $record['tgl_bayar']    = $tgl_bayar;
        $record['no_bayar']     = $no_bayar;
        $record['keterangan']   = $keterangan;
        $record['potongan']     = $potongan;
        $record['pembulatan']   = $pembulatan;
        $record['other_cost']   = $other_cost;
        $record['totpay']       = $totpay;

        $sql = "SELECT * FROM ap_payment WHERE appid = ?";
        $rs = DB::Execute($sql, [$appid]);

        if ($rs->EOF)
        {
            $record['bid']          = $bid;
            $record['create_by']    = $record['modify_by'] = $pid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            $appid = DB::GetOne("SELECT CURRVAL('ap_payment_appid_seq') AS code");
        }
        else
        {
            $appid = $rs->fields['appid'];

            $sqlu = "UPDATE ap_payment SET is_posted = 'f' WHERE appid = ?";
            $ok = DB::Execute($sqlu, [$appid]);

            $record['modify_by']    = $pid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqlu);
        }

        if (is_array($appdid))
        {
            $arr_appdid = "0";

            foreach ($appdid as $k => $v)
            {
                if ($pay[$k] <> 0)
                {
                    $sql = "SELECT (a.amount - COALESCE(SUM(b.nominal), 0)) AS outs
                            FROM ap_supplier a
                            LEFT JOIN ap_payment_d b ON a.apsid = b.apsid
                            WHERE a.apsid = ? AND a.bid = ?
                            GROUP BY a.amount";
                    $amount_outs = DB::GetOne($sql, [$apsid[$k], $bid]);

                    if ($amount[$k] > $amount_outs)
                    {
                        $msg = "Nominal Pembayaran Pada No A/P ".$apcode[$k]." Melebihi Nilai Outstanding";

                        DB::RollbackTrans();

                        return $msg;
                    }

                    $sql = "SELECT * FROM ap_payment_d WHERE appid = ? AND apsid = ?";
                    $rss = DB::Execute($sql, [$appid, $apsid[$k]]);

                    $recordd = array();
                    $recordd['appid']   = $appid;
                    $recordd['apsid']   = $apsid[$k];
                    $recordd['nominal'] = $pay[$k];
                    $recordd['pccid']   = $pccid[$k] ?? NULL;
                    $recordd['bid']     = $bid;

                    if ($rss->EOF)
                    {
                        $recordd['create_by']   = $recordd['modify_by'] = $pid;

                        $sqli = DB::InsertSQL($rss, $recordd);
                        if ($ok) $ok = DB::Execute($sqli);

                        $appdid = DB::GetOne("SELECT CURRVAL('ap_payment_d_appdid_seq') AS code");
                    }
                    else
                    {
                        $appdid = $rss->fields['appdid'];

                        $recordd['modify_by']    = $pid;
                        $recordd['modify_time']  = 'NOW()';

                        $sqlu = DB::UpdateSQL($rss, $recordd);
                        if ($ok) $ok = DB::Execute($sqlu);
                    }

                    $arr_appdid .= ",".$appdid;
                }
            }

            $sqld = "DELETE FROM ap_payment_d WHERE appid = ? AND appdid NOT IN ($arr_appdid)";
            if ($ok) $ok = DB::Execute($sqld, [$appid]);
        }

        if (is_array($appaid))
        {
            $arr_appaid = "0";

            foreach ($appaid as $k => $v)
            {
                $sql = "SELECT * FROM ap_payment_addless WHERE appid = ? AND appaid = ?";
                $rss = DB::Execute($sql, [$appid, $appaid[$k]]);

                $recordd = array();
                $recordd['appid']       = $appid;
                $recordd['coaid']       = $coaid[$k];
                $recordd['debet']       = $debet[$k];
                $recordd['credit']      = $credit[$k];
                $recordd['ket_addless'] = $ket_addless[$k];
                $recordd['bid']         = $bid;

                if ($rss->EOF)
                {
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $appaid = DB::GetOne("SELECT CURRVAL('ap_payment_addless_appaid_seq') AS code");
                }
                else
                {
                    $appaid = $rss->fields['appaid'];

                    $recordd['modify_by']    = $pid;
                    $recordd['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_appaid .= ",".$appaid;
            }

            $sqld = "DELETE FROM ap_payment_addless WHERE appid = ? AND appaid NOT IN ($arr_appaid)";
            if ($ok) $ok = DB::Execute($sqld, [$appid]);
        }

        $sql = "UPDATE ap_payment SET is_posted = 't' WHERE appid = ?";
        if ($ok) $ok = DB::Execute($sql, [$appid]);

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

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "UPDATE ap_payment SET is_posted = 'f' WHERE appid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM ap_payment_addless WHERE appid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM ap_payment_d WHERE appid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM ap_payment WHERE appid = ? AND bid = ?";
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