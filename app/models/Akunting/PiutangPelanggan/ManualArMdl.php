<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ManualArMdl extends DB
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
        $no_inv = strtolower(trim($data['no_inv']));
        $bank_id  = $data['bank_id'];
        $pegawai_id  = $data['pegawai_id'];

        if ($custid) $addsql .= " AND a.custid = ".$custid;

        if ($no_inv) $addsql .= " AND LOWER(a.no_inv) = '$no_inv'";

        if ($bank_id) $addsql .= " AND a.bank_id = ".$bank_id;
        if ($pegawai_id) $addsql .= " AND a.pegawai_id = ".$pegawai_id;

        $addsql .= " AND a.bid = ".$bid;


        $sql = "SELECT a.maid, a.ardate , a.arcode , a.no_inv, b.nama_customer
                        , a.totalall AS amount, ps.nama_lengkap AS useri, gl.glid, gl.suppid
                        , a.custid , a.pegawai_id , pd.nama_lengkap AS nama_pegawai,mb.bank_nama
                FROM manual_ar a
                INNER JOIN m_customer b ON b.custid  = a.custid
                LEFT JOIN person ps ON a.create_by = ps.pid
                LEFT JOIN general_ledger gl ON a.maid = gl.reff_id AND gl.jtid = 27
                LEFT JOIN person pd ON a.pegawai_id  = pd.pid
                LEFT JOIN m_bank mb ON a.bank_id  = mb.bank_id
                WHERE DATE(a.ardate) BETWEEN '$sdate' AND '$edate'
                    $addsql
                ORDER BY a.ardate DESC";

/*        echo '<pre>';
        print_r($sql);
        echo '</pre>';
*/
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_trans ($maid) /*}}}*/
    {
        //  if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.maid, b.madid, a.ardate, a.arcode, d.glid, a.custid,
                    format_glcode(d.gldate, e.doc_code, d.gldoc, d.glid) AS doc_no,
                    h.nama_lengkap as pegawai,i.bank_nama, a.no_inv, a.duedate, a.keterangan, 
                    f.coaid,f.coacode, f.coaname,
                    b.amount, c.nama_lengkap AS petugas, b.detailnote, a.subtotal, a.ppn, a.ppn_rp
                   ,j.nama_customer,i.bank_id,a.pegawai_id
                FROM manual_ar a
                INNER JOIN manual_ar_d b ON a.maid = b.maid
                INNER JOIN person c ON c.pid = a.create_by
                LEFT JOIN general_ledger d ON a.maid = d.reff_id AND d.jtid = 27
                LEFT JOIN journal_type e ON d.jtid = e.jtid
                INNER JOIN m_coa f ON f.coaid = b.coaid
                LEFT JOIN person h ON a.pegawai_id = h.pid
                LEFT JOIN m_bank i ON a.bank_id = i.bank_id
                LEFT JOIN m_customer j ON a.custid = j.custid
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
                WHERE a.allow_post = 't' AND a.is_valid = 't' AND a.coatid IN (4,5)
                ORDER BY a.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_trans ($mytype) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);
        // Header
        $mytype = get_var('mytype');
        $maid = get_var('maid', 0);
        $ardate = get_var('ardate');
        $duedate = get_var('duedate');
        $custid = get_var('custid', NULL);
        $pegawai_id = get_var('pegawai_id', NULL);
        $bank_id = get_var('bank_id', NULL);
        $no_inv = get_var('no_inv');
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

        if ($mytype == 2)
        {
            // upload form
            $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // resolve the first line Headers
            array_shift($data);

            $coaid_migrasi = get_var('coa_main');
//            print_r($data);

          foreach ($data as $idx => $row)
            {
                if($idx > 8 ){ // mulai di row ke 11 excel

                    $custid = DB::GetOne("SELECT custid FROM m_customer WHERE kode_customer = ?", array(trim($row['A'])));


                    if($custid == -1){
                        $pegawai_id     = DB::GetOne("SELECT pid FROM person WHERE nip = ?",trim($row['G']));

                        if($pegawai_id == ''){
                            DB::RollbackTrans();
                            $errmsg = "Pegawai dengan Kode " . $row['G']." Tidak Ditemukan";
                            return $errmsg;
                        }
                    }

                    if($custid == -2){
                        $bank_id     = DB::GetOne("SELECT bank_id FROM m_bank WHERE bank_kode = ?",trim($row['G']));
                        if($bank_id == ''){
                            DB::RollbackTrans();
                            $errmsg = "EDC dengan Kode " . $row['G']." Tidak Ditemukan";
                            return $errmsg;
                        }
                    }

                    $record['ardate']           = $ardate;
                    $record['duedate']          = $row['E'];
                    $record['custid']           = $custid;
                    $record['pegawai_id']       = $pegawai_id;
                    $record['bank_id']          = $bank_id;
                    $record['no_inv']           = $row['C'];
                    $record['keterangan']       = $keterangan;
                    $record['subtotal']         = $row['D'];
                    $record['totalall']         = $row['D'];
                    $record['bid']              = $bid;

                    $sql = "SELECT * FROM manual_ar WHERE maid = ?";
                    $rs = DB::Execute($sql, [$maid]);

                    $record['create_by']    = $record['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rs, $record);
                    $ok = DB::Execute($sqli);

                    $maid = DB::GetOne("SELECT CURRVAL('manual_ar_maid_seq') AS code");

                    $sql = "SELECT * FROM manual_ar_d WHERE 1=2";
                    $rss = DB::Execute($sql);

                    $recordd = array();
                    $recordd['maid']        = $maid;
                    $recordd['coaid']       = $coaid_migrasi;
                    $recordd['detailnote']  = $row['F'];
                    $recordd['amount']      = $row['D'];
                    $recordd['bid']         = $bid;
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $sql = "UPDATE manual_ar SET is_posted = 't' WHERE maid = ?";
                    if ($ok) $ok = DB::Execute($sql, [$maid]);


                }
            }


        }else{
            // manual form
                    // $record = array();
                    $record['ardate']           = $ardate;
                    $record['duedate']          = $duedate;
                    $record['custid']           = $custid;
                    $record['pegawai_id']       = $pegawai_id;
                    $record['bank_id']          = $bank_id;
                    $record['no_inv']           = $no_inv;
                    $record['keterangan']       = $keterangan;
                    $record['subtotal']         = $subtotal;
                    $record['ppn']              = $ppn;
                    $record['ppn_rp']           = $ppn_rp;
                    $record['totalall']         = $totalall;
                    $record['bid']              = $bid;

                    $sql = "SELECT * FROM manual_ar WHERE maid = ?";
                    $rs = DB::Execute($sql, [$maid]);

                    if ($rs->EOF)
                    {
                        $record['create_by']    = $record['modify_by'] = $pid;

                        $sqli = DB::InsertSQL($rs, $record);
                        $ok = DB::Execute($sqli);

                        $maid = DB::GetOne("SELECT CURRVAL('manual_ar_maid_seq') AS code");
                    }
                    else
                    {
                        $maid = $rs->fields['maid'];

                        $sqlu = "UPDATE manual_ar SET is_posted = 'f' WHERE maid = ?";
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
                            $sql = "SELECT * FROM manual_ar_d WHERE maid = ? AND coaid = ?";
                            $rss = DB::Execute($sql, [$maid, $coaid[$k]]);

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

                                $madid = DB::GetOne("SELECT CURRVAL('manual_ar_d_madid_seq') AS code");
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

                        $sqld = "DELETE FROM manual_ar_d WHERE maid = ? AND madid NOT IN ($arr_madid)";
                        if ($ok) $ok = DB::Execute($sqld, [$maid]);
                    }

                    $sql = "UPDATE manual_ar SET is_posted = 't' WHERE maid = ?";
                    if ($ok) $ok = DB::Execute($sql, [$maid]);
            }

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            die('<br>dharmadhiester debug');
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

        $cek_data = floatval(DB::GetOne("SELECT COUNT(*) FROM penerimaan_manual_ar_d WHERE maid = ? AND bid = ?", [$myid, $bid]));

        if ($cek_data > 0) return 'Invoice Sudah Ada Pembayaran';

        DB::BeginTrans();

        $sql = "UPDATE manual_ar SET is_posted = 'f' WHERE maid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ar_d WHERE maid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM manual_ar WHERE maid = ? AND bid = ?";
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
