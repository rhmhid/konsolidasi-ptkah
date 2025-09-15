<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class JurnalManualMdl extends DB
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
        $jurnal_speriod = $data['jurnal_speriod'];
        $jurnal_eperiod = $data['jurnal_eperiod'];
        $is_posted = $data['is_posted'];
        $gldoc = strtolower(trim($data['gldoc']));
        $keterangan = strtolower(trim($data['keterangan']));

        if ($is_posted) $addsql .= " AND a.is_posted = '$is_posted'";

        if ($gldoc) $addsql .= " AND a.trans_code = '$gldoc'";

        if ($keterangan) $addsql .= " AND LOWER(a.keterangan) LIKE '%$keterangan%'";

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.jmid, a.trans_date, a.keterangan, b.nama_lengkap AS useri, a.is_posted
                    , a.trans_code AS gldoc, c.glid
                FROM jurnal_manual a
                JOIN person b ON b.pid = a.create_by
                LEFT JOIN general_ledger c ON a.jmid = c.reff_id AND c.jtid = 3
                WHERE DATE(a.trans_date) BETWEEN DATE('$jurnal_speriod') AND DATE('$jurnal_eperiod')
                    $addsql
                ORDER BY a.trans_date DESC, a.jmid DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function data_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' ' || a.coaname) AS coa, a.coaid, a.coatid
                    , a.coacode, a.coaname
                FROM m_coa a
                WHERE a.allow_post = 't' AND a.is_valid = 't' AND a.is_manual_journal = 't'
                ORDER BY a.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_cost_center () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (pcccode || ' - ' || pccname) AS pcc, pccid
                    , pcccode, pccname
                FROM profit_cost_center
                WHERE is_aktif = 't'
                ORDER BY pcccode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_jurnal ($mytype) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $mytype = get_var('mytype');
        $jmid = get_var('jmid', 0);
        $trans_date = get_var('trans_date');
        $keterangan = get_var('keterangan');
        $is_posted = get_var('is_posted', 'f');

        // Detail
        $coaid = get_var('coaid');
        $notes = get_var('notes');
        $debet = get_var('debet');
        $credit = get_var('credit');
        $pccid = get_var('pccid');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $tot_deb = $tot_cre = $unbal = 0;

        if ($mytype == 2)
        {
            $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

            // Set sheet aktif, misalnya sheet ke-1 (index 0)
            $spreadsheet->setActiveSheetIndex(0);

            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // resolve the first line Headers
            array_shift($data);

            $coaid = $notes = $debet = $credit = $pccid = array();
            foreach ($data as $idx => $row)
            {
                $data_coa = DB::Execute("SELECT coaid, coatid FROM m_coa WHERE coacode = ?", [$row['A']]);

                if (intval($data_coa->fields['coaid']) == 0)
                    return 'Kode COA '.$row['A'].' Tidak Ditemukan Dalam Database';

                $coaid[] = $data_coa->fields['coaid'];
                $notes[] = $row['C'];
                $debet[] = floatval($row['D']);
                $credit[] = floatval($row['E']);

                if ($data_coa->fields['coaid'] > 3 && $row['F'] != '')
                {
                    $id_cost = DB::GetOne("SELECT pccid FROM profit_cost_center WHERE pcccode = ?", [$row['F']]);

                    if (intval($id_cost) == 0)
                        return 'Kode Cost Center '.$row['F'].' Tidak Ditemukan Dalam Database';
                }
                else
                    $id_cost = NULL;

                $pccid[] = $id_cost;
            }
        }

        DB::BeginTrans();

        $record = array();
        $record['trans_date']   = $trans_date;
        $record['keterangan']   = $keterangan;
        $record['is_posted']    = 'f';
        $record['create_by']    = $record['modify_by'] = $pid;
        $record['bid']          = $bid;

        $sql = "SELECT * FROM jurnal_manual WHERE jmid = ?";
        $rs = DB::Execute($sql, array($jmid));
        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

        $newjmid = DB::GetOne("SELECT CURRVAL('jurnal_manual_jmid_seq') AS code");

        if (is_array($coaid))
        {
            foreach ($coaid as $id_coa => $val)
            {
                $recordd = array();
                $recordd['jmid']        = $newjmid;
                $recordd['coaid']       = $val;
                $recordd['notes']       = $notes[$id_coa];
                $recordd['debet']       = $debet[$id_coa];
                $recordd['credit']      = $credit[$id_coa];
                $recordd['pccid']       = $pccid[$id_coa] ? $pccid[$id_coa] : NULL;
                $recordd['create_by']   = $recordd['modify_by'] = $pid;
                $recordd['bid']         = $bid;

                $sql = "SELECT * FROM jurnal_manual_d WHERE jmid = ?";
                $rs = DB::Execute($sql, array($jmid));
                $sqli = DB::InsertSQL($rs, $recordd);
                if ($ok) $ok = DB::Execute($sqli);

                $tot_deb += $debet[$id_coa];
                $tot_cre += $credit[$id_coa];
            }
        }

        if ($is_posted == 't')
        {
            $sql = "UPDATE jurnal_manual SET is_posted = 't' WHERE jmid = ?";
            if ($ok) $ok = DB::Execute($sql, array($newjmid));
        }

        $unbal = $tot_deb - $tot_cre;

        if ($unbal <> 0)
        {
            DB::RollbackTrans();
            return 'Transaksi Tidak Balance Sejumlah Rp. '.format_uang($unbal, 2);
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

    public static function posting_jurnal ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "UPDATE jurnal_manual SET is_posted = 't', modify_by = $pid, modify_time = NOW() WHERE jmid = ? AND bid = ?";
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

    public static function delete_jurnal ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "UPDATE jurnal_manual SET is_posted = 'f' WHERE jmid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM jurnal_manual_d WHERE jmid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM jurnal_manual WHERE jmid = ? AND bid = ?";
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