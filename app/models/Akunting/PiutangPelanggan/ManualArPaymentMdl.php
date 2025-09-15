<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class ManualArPaymentMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        //   if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $custid = $data['custid'];
        $no_pay = strtolower(trim($data['no_pay']));
        $bank_id = $data['bank_id'];
        $pegawai_id = $data['pegawai_id'];

        if ($custid) $addsql .= " AND a.custid = ".$custid;

        if ($no_pay) $addsql .= " AND a.paycode = '$no_pay'";

        if ($bank_id) $addsql .= " AND a.bank_ar = ".$bank_id;
        if ($pegawai_id) $addsql .= " AND a.pegawai_id = ".$pegawai_id;

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.mapid, a.paydate, a.paycode, a.no_terima, b.nama_customer, a.tot_terima AS amount
                    , ps.nama_lengkap AS useri, gl.glid, a.custid,pg.nama_lengkap as nama_pegawai,mb.bank_nama
                FROM manual_ar_payment a
                INNER JOIN m_customer b ON b.custid = a.custid
                LEFT JOIN general_ledger gl ON a.mapid = gl.reff_id AND gl.jtid = 28
                LEFT JOIN person ps ON a.create_by = ps.pid
                LEFT JOIN person pg ON a.pegawai_id = pg.pid
                LEFT JOIN m_bank mb ON a.bank_ar = mb.bank_id
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
        //if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.mapid, a.paydate, a.custid, a.bank_id, e.glid,
                    format_glcode(e.gldate, f.doc_code, e.gldoc, e.glid) AS doc_no,
                    a.cara_terima, a.no_terima, a.paycode, a.keterangan,
                    c.nama_customer, g.arcode, g.ardate, g.no_inv,
                    g.totalall AS nominal_inv, b.amount AS nominal_piutang,
                    a.potongan, a.pembulatan, a.other_cost, a.tot_terima,
                    b.mapdid, b.maid, d.nama_lengkap AS petugas, h.bank_nama
                    , a.pegawai_id, pd.nama_lengkap AS nama_dokter
                FROM manual_ar_payment a
                INNER JOIN manual_ar_payment_d b ON a.mapid = b.mapid
                INNER JOIN m_customer c ON c.custid = a.custid
                INNER JOIN person d ON d.pid = a.create_by
                LEFT JOIN general_ledger e ON a.mapid = e.reff_id AND e.jtid = 28
                LEFT JOIN journal_type f ON e.jtid = f.jtid
                LEFT JOIN manual_ar g ON b.maid = g.maid
                INNER JOIN m_bank h ON h.bank_id = a.bank_id
                LEFT JOIN person pd ON a.pegawai_id = pd.pid
                WHERE a.mapid = ? AND a.bid = ?
                ORDER BY b.mapdid";
        $rs = DB::Execute($sql, [$mapid, $bid]);

        return $rs;
    } /*}}}*/


    public static function detail_trans_addless ($mapid) /*}}}*/
    {
        //  if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

            $sql = "SELECT a.mapaid, a.coaid, (b.coacode || ' - ' || b.coaname) AS coa,
                        a.debet, a.credit, a.ket_addless
                    FROM manual_ar_payment_addless a
                    INNER JOIN m_coa b ON b.coaid = a.coaid
                    WHERE a.mapid = ? AND a.bid = ?
                    ORDER BY a.mapaid";
        $rs = DB::Execute($sql, [$mapid, $bid]);

        return $rs;
    } /*}}}*/


    public static function list_inv ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        //    if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = "";
        $custid = get_var("custid");
        $bank_id = get_var("bank_ar");
        $pegawai_id = get_var("pegawai_id");
        $no_inv = get_var("no_inv");

        $addSql = "";

        if ($custid) $addSql .= " AND a.custid = ".$custid;
        if ($bank_id) $addSql .= " AND a.bank_id = ".$bank_id;
        if ($pegawai_id) $addSql .= " AND a.pegawai_id = ".$pegawai_id;
        if ($no_inv) $addSql .= " AND upper(a.no_inv || a.keterangan) like upper('%".$no_inv."%') ";

        $addSql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.maid, a.arcode, a.ardate, a.no_inv, a.totalall AS nominal_inv, a.keterangan
                        , (a.totalall - COALESCE(c.terima, 0)) AS sisa, a.custid, a.pegawai_id, d.nama_lengkap AS nama_pegawai,e.bank_nama,b.nama_customer
                    FROM manual_ar a
                    INNER JOIN m_customer b ON b.custid = a.custid
                    LEFT JOIN (SELECT x.maid, SUM(x.amount) AS terima FROM manual_ar_payment_d x GROUP BY x.maid) c ON a.maid = c.maid
                    LEFT JOIN person d ON a.pegawai_id = d.pid
                    LEFT JOIN m_bank e ON a.bank_id = e.bank_id
                    WHERE (a.totalall - COALESCE(c.terima, 0)) > 0 
                     AND a.bid = $bid 
                     $addSql
                    ORDER BY a.ardate, a.maid DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/


    public static function data_invoice () /*{{{*/
    {
        //        if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $key = get_var("q");
        $custid = get_var("custid");
        $bank_id = get_var("bank_ar");
        $pegawai_id = get_var("pegawai_id");

        $addSql = "";

        if ($bank_id) $addSql .= " AND a.bank_id = ".$bank_id;
        if ($pegawai_id) $addSql .= " AND a.pegawai_id = ".$pegawai_id;

            $sql = "SELECT a.maid, a.arcode, a.ardate, a.no_inv, a.totalall AS nominal_inv, a.keterangan
                        , (a.totalall - COALESCE(c.terima, 0)) AS sisa, a.custid, a.pegawai_id, d.nama_lengkap AS karyawan
                    FROM manual_ar a
                    INNER JOIN m_customer b ON b.custid = a.custid
                    LEFT JOIN (SELECT x.maid, SUM(x.amount) AS terima FROM manual_ar_payment_d x GROUP BY x.maid) c ON a.maid = c.maid
                    LEFT JOIN person d ON a.pegawai_id = d.pid
                    WHERE a.custid = ? AND (a.totalall - COALESCE(c.terima, 0)) > 0 
                     AND a.bid = $bid 
                     AND upper(a.no_inv) like upper('%".$key."%') 
                     $addSql
                    ORDER BY a.ardate, a.maid DESC";




        $rs = DB::Execute($sql, [$custid]);

        return $rs;
    } /*}}}*/

    public static function save_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $mapid = get_var('mapid', 0);
        $paydate = get_var('paydate');
        $custid = get_var('custid', NULL);
        $bank_id = get_var('bank_id', NULL);
        $bank_ar = get_var('bank_ar', NULL);
        $pegawai_id = get_var('pegawai_id', NULL);
        $cara_terima = get_var('cara_terima', NULL);
        $no_terima = get_var('no_terima');
        $keterangan = get_var('keterangan');
        $subtotal = get_var('subtotal_pay');
        $potongan = get_var('potongan');
        $pembulatan = get_var('pembulatan');
        $other_cost = get_var('other_cost');
        $tot_terima = get_var('tot_terima');

        // Detail
        $mapdid = get_var('mapdid');
        $maid = get_var('maid');
        $apcode = get_var('apcode');
        $amount = get_var('nominal_terima');

        $mapaid = get_var('mapaid');
        $coaid = get_var('coaid');
        $debet = get_var('debet');
        $credit = get_var('credit');
        $ket_addless = get_var('ket_addless');


        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['paydate']      = $paydate;
        $record['custid']       = $custid;
        $record['bank_id']      = $bank_id;
        $record['bank_ar']      = $bank_ar;
        $record['pegawai_id']   = $pegawai_id;
        $record['cara_terima']  = $cara_terima;
        $record['no_terima']    = $no_terima;
        $record['keterangan']   = $keterangan;
        $record['subtotal']     = $subtotal;
        $record['potongan']     = $potongan;
        $record['pembulatan']   = $pembulatan;
        $record['other_cost']   = $other_cost;
        $record['tot_terima']   = $tot_terima;
        $record['bid']          = $bid;

        $sql = "SELECT * FROM manual_ar_payment WHERE mapid = ?";
        $rs = DB::Execute($sql, [$mapid]);

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $pid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            $mapid = DB::GetOne("SELECT CURRVAL('manual_ar_payment_mapid_seq') AS code");
        }
        else
        {
            $mapid = $rs->fields['mapid'];

            $sqlu = "UPDATE manual_ar_payment SET is_posted = 'f' WHERE mapid = ?";
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
                        FROM manual_ar a
                        LEFT JOIN manual_ar_payment_d b ON a.maid = b.maid
                        WHERE a.maid = ? AND a.bid = ?
                        GROUP BY a.totalall";
                $amount_outs = DB::GetOne($sql, [$maid[$k], $bid]);

                if ($amount[$k] > $amount_outs)
                {
                    $msg = "Nominal Pembayaran Pada No A/R ".$apcode[$k]." Melebihi Nilai Outstanding";

                    DB::RollbackTrans();

                    return $msg;
                }

                $sql = "SELECT * FROM manual_ar_payment_d WHERE mapid = ? AND maid = ?";
                $rss = DB::Execute($sql, [$mapid, $maid[$k]]);

                $recordd = array();
                $recordd['mapid']       = $mapid;
                $recordd['maid']        = $maid[$k];
                $recordd['amount']      = $amount[$k];
                $recordd['pegawai_id']  = $pegawai_id;
                $recordd['bank_ad']     = $bank_ar;
                $recordd['bid']         = $bid;

                if ($rss->EOF)
                {
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $mapdid = DB::GetOne("SELECT CURRVAL('manual_ar_payment_d_mapdid_seq') AS code");
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

            $sqld = "DELETE FROM manual_ar_payment_d WHERE maid = ? AND mapdid NOT IN ($arr_mapdid)";
            if ($ok) $ok = DB::Execute($sqld, [$mapid]);
        }

        if (is_array($coaid))
        {
            $arr_mapaid = "";

            foreach ($coaid as $k => $v)
            {


                $sql = "SELECT * FROM manual_ar_payment_addless WHERE mapid = ? AND mapaid = ?";
                $rss = DB::Execute($sql, [$mapid, $mapaid[$k]]);

                $record_d = array();
                $record_d['mapid']          = $mapid;
                $record_d['coaid']          = $v;
                $record_d['debet']          = $debet[$k];
                $record_d['credit']         = $credit[$k];
                $record_d['ket_addless']    = $ket_addless[$k];
                $record_d['bid']            = $bid;

                if ($rss->EOF)
                {
                    $record_d['create_by']   = $record_d['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $record_d);
                    if ($ok) $ok = DB::Execute($sqli);

                    $newmapaid = DB::GetOne("SELECT CURRVAL('manual_ar_payment_addless_mapaid_seq')");
                }
                else
                {
                    $newmapaid = $rss->fields['mapaid'];

                    $record_d['modify_by']    = $pid;
                    $record_d['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $record_d);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_mapaid .= $newmapaid.",";
            }

            $arr_mapaid .= "0";

            $sqld = "DELETE FROM manual_ar_payment_addless WHERE mapaid NOT IN ($arr_mapaid) AND mapid = ?";
            if ($ok) $ok = DB::Execute($sqld, [$mapid]);
        }

        $sql = "UPDATE manual_ar_payment SET is_posted = 't' WHERE mapid = ?";
        if ($ok) $ok = DB::Execute($sql, [$mapid]);

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
        //  if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "UPDATE manual_ar_payment SET is_posted = 'f' WHERE mapid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ar_payment_addless WHERE mapid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ar_payment_d WHERE mapid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);


        $sql = "DELETE FROM manual_ar_payment WHERE mapid = ? AND bid = ?";
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
