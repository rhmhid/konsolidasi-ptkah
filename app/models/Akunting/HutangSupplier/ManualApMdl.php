<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class ManualApMdl extends DB
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
        $no_inv = strtolower(trim($data['no_inv']));
        $doctor_id = $data['doctor_id'];

        if ($suppid) $addsql .= " AND a.suppid = ".$suppid;

        if ($no_inv) $addsql .= " AND a.no_inv = '$no_inv'";

        if ($doctor_id) $addsql .= " AND a.doctor_id = ".$doctor_id;

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.maid, a.apdate, a.apcode, a.no_inv, b.nama_supp
                    , a.totalall AS amount, ps.nama_lengkap AS useri, gl.glid
                    , a.suppid, a.doctor_id, pd.nama_lengkap AS nama_dokter
                FROM manual_ap a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN person ps ON a.create_by = ps.pid
                LEFT JOIN general_ledger gl ON a.maid = gl.reff_id AND gl.jtid = 22
                LEFT JOIN person pd ON a.doctor_id = pd.pid
                WHERE DATE(a.apdate) BETWEEN '$sdate' AND '$edate'
                    $addsql
                ORDER BY a.apdate DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_trans ($maid) /*}}}*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.maid, b.madid, a.apdate, a.apcode, a.suppid, f.glid,
                    format_glcode(f.gldate, g.doc_code, f.gldoc, f.glid) AS doc_no,
                    c.nama_supp, a.no_inv, a.duedate, a.keterangan, a.faktur_pajak,
                    a.tgl_faktur_pajak, b.amount, d.nama_lengkap AS petugas, e.coaid,
                    e.coacode, e.coaname, b.detailnote, a.subtotal, a.ppn, a.ppn_rp
                    , a.doctor_id, h.nama_lengkap AS nama_dokter, b.pccid
                FROM manual_ap a
                INNER JOIN manual_ap_d b ON a.maid = b.maid
                INNER JOIN m_supplier c ON c.suppid = a.suppid
                INNER JOIN person d ON d.pid = a.create_by
                INNER JOIN m_coa e ON e.coaid = b.coaid
                LEFT JOIN general_ledger f ON a.maid = f.reff_id AND f.jtid = 22
                LEFT JOIN journal_type g ON f.jtid = g.jtid
                LEFT JOIN person h ON a.doctor_id = h.pid
                WHERE a.maid = ? AND a.bid = ?
                ORDER BY b.madid";
        $rs = DB::Execute($sql, [$maid, $bid]);

        return $rs;
    } /*}}}*/

    public static function data_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' ' || a.coaname) AS coa, a.coaid, a.coatid
                FROM m_coa a
                WHERE a.allow_post = 't' AND a.is_valid = 't' AND a.coatid IN (1, 5)
                ORDER BY a.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $maid = get_var('maid', 0);
        $apdate = get_var('apdate');
        $duedate = get_var('duedate');
        $suppid = get_var('suppid', NULL);
        $doctor_id = get_var('doctor_id', NULL);
        $faktur_pajak = get_var('faktur_pajak');
        $no_inv = get_var('no_inv');
        $tgl_faktur_pajak = get_var('tgl_faktur_pajak');
        $keterangan = get_var('keterangan');
        $subtotal = get_var('subtotal');
        $ppn = get_var('ppn');
        $ppn_rp = get_var('ppn_rp');
        $totalall = get_var('totalall');

        // Detail
        $madid = get_var('madid');
        $coaid = get_var('coaid');
        $detailnote = get_var('notes');
        $amount = get_var('amount');
        $pccid = get_var('pccid');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['apdate']           = $apdate;
        $record['duedate']          = $duedate;
        $record['suppid']           = $suppid;
        $record['doctor_id']        = $doctor_id;
        $record['faktur_pajak']     = $faktur_pajak;
        $record['no_inv']           = $no_inv;
        $record['tgl_faktur_pajak'] = $tgl_faktur_pajak;
        $record['keterangan']       = $keterangan;
        $record['subtotal']         = $subtotal;
        $record['ppn']              = $ppn;
        $record['ppn_rp']           = $ppn_rp;
        $record['totalall']         = $totalall;

        $sql = "SELECT * FROM manual_ap WHERE maid = ?";
        $rs = DB::Execute($sql, [$maid]);

        if ($rs->EOF)
        {
            $record['bid']          = $bid;
            $record['create_by']    = $record['modify_by'] = $pid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            $maid = DB::GetOne("SELECT CURRVAL('manual_ap_maid_seq') AS code");
        }
        else
        {
            $maid = $rs->fields['maid'];

            $cek_data = floatval(DB::GetOne("SELECT COUNT(*) FROM manual_ap_payment_d WHERE maid = ? AND bid = ?", [$maid, $bid]));

            if ($cek_data > 0)
            {
                DB::RollbackTrans();

                return 'Invoice Sudah Ada Pembayaran';
            }

            $sqlu = "UPDATE manual_ap SET is_posted = 'f' WHERE maid = ?";
            $ok = DB::Execute($sqlu, [$maid]);

            $record['modify_by']    = $pid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqlu);
        }

        if (is_array($madid))
        {
            $arr_madid = "";

            foreach ($madid as $k => $v)
            {
                $sql = "SELECT * FROM manual_ap_d WHERE maid = ? AND coaid = ? AND madid = ?";
                $rss = DB::Execute($sql, [$maid, $coaid[$k], $madid[$k]]);

                $recordd = array();
                $recordd['maid']        = $maid;
                $recordd['coaid']       = $coaid[$k];
                $recordd['detailnote']  = $detailnote[$k];
                $recordd['amount']      = $amount[$k];
                $recordd['pccid']       = $pccid[$k] ? $pccid[$k] : NULL;
                $recordd['bid']         = $bid;

                if ($rss->EOF)
                {
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $madid = DB::GetOne("SELECT CURRVAL('manual_ap_d_madid_seq') AS code");
                }
                else
                {
                    $madid = $rss->fields['madid'];

                    $recordd['modify_by']    = $pid;
                    $recordd['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_madid .= $madid.",";
            }

            $arr_madid .= "0";

            $sqld = "DELETE FROM manual_ap_d WHERE maid = ? AND madid NOT IN ($arr_madid)";
            if ($ok) $ok = DB::Execute($sqld, [$maid]);
        }

        $sql = "UPDATE manual_ap SET is_posted = 't' WHERE maid = ?";
        if ($ok) $ok = DB::Execute($sql, [$maid]);

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

        $cek_data = floatval(DB::GetOne("SELECT COUNT(*) FROM manual_ap_payment_d WHERE maid = ? AND bid = ?", [$myid, $bid]));

        if ($cek_data > 0) return 'Invoice Sudah Ada Pembayaran';

        DB::BeginTrans();

        $sql = "UPDATE manual_ap SET is_posted = 'f' WHERE maid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ap_d WHERE maid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ap WHERE maid = ? AND bid = ?";
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
