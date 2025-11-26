<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class ManualApPaymentMdl extends DB
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
        $no_pay = strtolower(trim($data['no_pay']));
        $doctor_id = $data['doctor_id'];

        if ($suppid) $addsql .= " AND a.suppid = ".$suppid;

        if ($no_pay) $addsql .= " AND a.no_pay = '$no_pay'";

        if ($doctor_id) $addsql .= " AND a.doctor_id = ".$doctor_id;

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.mapid, a.paydate, a.paycode, a.no_bayar, b.nama_supp, a.totpay AS amount
                    , ps.nama_lengkap AS useri, gl.glid, a.suppid, a.doctor_id, pd.nama_lengkap AS nama_dokter
                FROM manual_ap_payment a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN general_ledger gl ON a.mapid = gl.reff_id AND gl.jtid = 23
                LEFT JOIN person ps ON a.create_by = ps.pid
                LEFT JOIN person pd ON a.doctor_id = pd.pid
                WHERE DATE(a.paydate) BETWEEN '$sdate' AND '$edate'
                    $addsql
                ORDER BY a.paydate DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_trans ($mapid) /*}}}*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.mapid, a.paydate, a.suppid, a.bank_id, e.glid,
                    format_glcode(e.gldate, f.doc_code, e.gldoc, e.glid) AS doc_no,
                    a.cara_bayar, a.no_bayar, a.paycode, a.keterangan,
                    c.nama_supp, g.apcode, g.apdate, g.no_inv,
                    g.totalall AS nominal_inv, b.amount AS nominal_hutang,
                    a.potongan, a.pembulatan, a.other_cost, a.tax_doctor, a.totpay,
                    b.mapdid, b.maid, d.nama_lengkap AS petugas, h.bank_nama
                    , a.doctor_id, pd.nama_lengkap AS nama_dokter
                FROM manual_ap_payment a
                INNER JOIN manual_ap_payment_d b ON a.mapid = b.mapid
                INNER JOIN m_supplier c ON c.suppid = a.suppid
                INNER JOIN person d ON d.pid = a.create_by
                LEFT JOIN general_ledger e ON a.mapid = e.reff_id AND e.jtid = 23
                LEFT JOIN journal_type f ON e.jtid = f.jtid
                LEFT JOIN manual_ap g ON b.maid = g.maid
                INNER JOIN m_bank h ON h.bank_id = a.bank_id
                LEFT JOIN person pd ON a.doctor_id = pd.pid
                WHERE a.mapid = ? AND a.bid = ?
                ORDER BY b.mapdid";
        $rs = DB::Execute($sql, [$mapid, $bid]);

        return $rs;
    } /*}}}*/

    public static function data_invoice () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $key = get_var("q");
        $suppid = get_var("suppid");
        $doctor_id = get_var("doctor_id");
        $addSql = "";

	if ($doctor_id) $addSql .= " AND a.doctor_id = ".$doctor_id;
        if ($key) $addSql .= " AND upper(a.no_inv) like upper('%".$key."%')";

        $sql = "SELECT a.maid, a.apcode, a.apdate, a.no_inv
                    , a.totalall AS nominal_inv, a.keterangan
                    , (a.totalall - COALESCE(c.pay, 0)) AS sisa
                FROM manual_ap a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN (
                    SELECT x.maid, SUM(x.amount) AS pay
                    FROM manual_ap_payment_d x
                    WHERE x.bid = $bid
                    GROUP BY x.maid
                ) c ON a.maid = c.maid
                WHERE a.suppid = ? AND (a.totalall - COALESCE(c.pay, 0)) > 0
                    AND a.bid = $bid $addSql
                ORDER BY a.apdate, a.maid DESC";
	$rs = DB::Execute($sql, [$suppid]);

        return $rs;
    } /*}}}*/

    public static function save_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $mapid = get_var('mapid', 0);
        $paydate = get_var('paydate');
        $suppid = get_var('suppid', NULL);
        $bank_id = get_var('bank_id', NULL);
        $doctor_id = get_var('doctor_id', NULL);
        $cara_bayar = get_var('cara_bayar', NULL);
        $no_bayar = get_var('no_bayar');
        $keterangan = get_var('keterangan');
        $subtotal = get_var('subtotal_pay');
        $tax_doctor = get_var('tax_doctor');
        $potongan = get_var('potongan');
        $pembulatan = get_var('pembulatan');
        $other_cost = get_var('other_cost');
        $totpay = get_var('totpay');

        // Detail
        $mapdid = get_var('mapdid');
        $maid = get_var('maid');
        $apcode = get_var('apcode');
        $amount = get_var('nominal_terima');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['paydate']      = $paydate;
        $record['suppid']       = $suppid;
        $record['bank_id']      = $bank_id;
        $record['doctor_id']    = $doctor_id;
        $record['cara_bayar']   = $cara_bayar;
        $record['no_bayar']     = $no_bayar;
        $record['keterangan']   = $keterangan;
        $record['subtotal']     = $subtotal;
        $record['tax_doctor']   = $tax_doctor;
        $record['potongan']     = $potongan;
        $record['pembulatan']   = $pembulatan;
        $record['other_cost']   = $other_cost;
        $record['totpay']       = $totpay;
        $record['bid']          = $bid;

        $sql = "SELECT * FROM manual_ap_payment WHERE mapid = ?";
        $rs = DB::Execute($sql, [$mapid]);

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $pid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            $mapid = DB::GetOne("SELECT CURRVAL('manual_ap_payment_mapid_seq') AS code");
        }
        else
        {
            $mapid = $rs->fields['mapid'];

            $sqlu = "UPDATE manual_ap_payment SET is_posted = 'f' WHERE mapid = ?";
            $ok = DB::Execute($sqlu, [$mapid]);

            $record['modify_by']    = $pid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqlu);
        }

        if (is_array($mapdid))
        {
            $arr_mapdid = "";

            foreach ($mapdid as $k => $v)
            {
                $sql = "SELECT (a.totalall - COALESCE(SUM(b.amount), 0)) AS outs
                        FROM manual_ap a
                        LEFT JOIN manual_ap_payment_d b ON a.maid = b.maid
                        WHERE a.maid = ? AND a.bid = ?
                        GROUP BY a.totalall";
                $amount_outs = DB::GetOne($sql, [$maid[$k], $bid]);

                if ($amount[$k] > $amount_outs)
                {
                    $msg = "Nominal Pembayaran Pada No A/P ".$apcode[$k]." Melebihi Nilai Outstanding";

                    DB::RollbackTrans();

                    return $msg;
                }

                $sql = "SELECT * FROM manual_ap_payment_d WHERE mapid = ? AND maid = ?";
                $rss = DB::Execute($sql, [$mapid, $maid[$k]]);

                $recordd = array();
                $recordd['mapid']   = $mapid;
                $recordd['maid']    = $maid[$k];
                $recordd['amount']  = $amount[$k];
                $recordd['bid']     = $bid;

                if ($rss->EOF)
                {
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $mapdid = DB::GetOne("SELECT CURRVAL('manual_ap_payment_d_mapdid_seq') AS code");
                }
                else
                {
                    $mapdid = $rss->fields['mapdid'];

                    $recordd['modify_by']    = $pid;
                    $recordd['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_mapdid .= $mapdid.",";
            }

            $arr_mapdid .= "0";

            $sqld = "DELETE FROM manual_ap_payment_d WHERE mapid = ? AND mapdid NOT IN ($arr_mapdid)";
            if ($ok) $ok = DB::Execute($sqld, [$mapid]);
        }

        $sql = "UPDATE manual_ap_payment SET is_posted = 't' WHERE mapid = ?";
        if ($ok) $ok = DB::Execute($sql, [$mapid]);

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

        $sql = "UPDATE manual_ap_payment SET is_posted = 'f' WHERE mapid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ap_payment_d WHERE mapid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ap_payment WHERE mapid = ? AND bid = ?";
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


    public static function list_inv ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        //           if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = "";
        $suppid = get_var("suppid");
        $doctor_id = get_var("doctor_id");
        $no_inv = get_var("no_inv");

        $addSql = "";

        $suppid = get_var("suppid");
        $doctor_id = get_var("doctor_id");

        if ($suppid) $addSql .= " AND a.suppid = ".$suppid;
        if ($doctor_id) $addSql .= " AND a.doctor_id = ".$doctor_id;
        if ($key) $addSql .= " AND upper(a.no_inv) like upper('%".$key."%')";
        if ($no_inv) $addSql .= " AND upper(a.no_inv || a.keterangan) like upper('%".$no_inv."%') ";



        $sql = "SELECT a.maid, a.apcode, a.apdate, a.no_inv
                    , a.totalall AS nominal_inv, a.keterangan,b.nama_supp
                    , (a.totalall - COALESCE(c.pay, 0)) AS sisa
                FROM manual_ap a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN (
                    SELECT x.maid, SUM(x.amount) AS pay
                    FROM manual_ap_payment_d x
                    WHERE x.bid = $bid
                    GROUP BY x.maid
                ) c ON a.maid = c.maid
                WHERE (a.totalall - COALESCE(c.pay, 0)) > 0
                    AND a.bid = $bid $addSql
                ORDER BY a.apdate, a.maid DESC";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;


    } /*}}}*/

}
?>
