<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class DaftarJurnalMdl extends DB
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
        $jurnal_speriod = $data['jurnal_speriod'];
        $jurnal_eperiod = $data['jurnal_eperiod'];
        $jtid = $data['jtid'];
        $is_posted = $data['is_posted'];
        $gldoc = strtolower(trim($data['gldoc']));
        $keterangan = strtolower(trim($data['keterangan']));

        if ($jtid) $addsql .= " AND a.jtid = ".$jtid;

        if ($is_posted) $addsql .= " AND a.is_posted = '$is_posted'";

        if ($gldoc) $addsql .= " AND format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) = '$gldoc'";

        if ($keterangan) $addsql .= " AND LOWER(a.keterangan) LIKE '%$keterangan%'";

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.glid, a.gldate, a.gldesc, c.nama_lengkap AS useri, a.is_posted
                    , format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) AS gldoc
                    , b.journal_name
                FROM general_ledger a
                INNER JOIN journal_type b ON b.jtid = a.jtid
                JOIN person c ON c.pid = a.create_by
                WHERE DATE(a.gldate) BETWEEN DATE('$jurnal_speriod') AND DATE('$jurnal_eperiod')
                    $addsql
                ORDER BY a.gldate DESC, a.glid DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_jurnal ($myglid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.gldid, b.glid, b.gldesc, b.gldate, b.is_posted, d.journal_name, b.create_time
                    , format_glcode(b.gldate, d.doc_code, b.gldoc, b.glid) AS gldoc
                    , (CASE WHEN b.is_posted THEN 'POSTED' ELSE 'NOT POSTED' END) AS posted
                    , b.jtid, c.coacode, c.coaname, a.debet, a.credit, a.notes, e.nama_lengkap AS posted_by
                    , (CASE 
                            WHEN b.jtid IN (4, 9, 20, 21, 22, 23, 24, 25, 26, 29) 
                                THEN f.nama_supp 
                            WHEN b.jtid IN (27,28) AND b.suppid = -1  -- PIUTANG PEGAWAI
                                THEN j.nama_lengkap 
                            WHEN b.jtid IN (27,28) AND b.suppid = -2  -- PIUTANG EDC
                                THEN i.bank_nama 
                       ELSE g.nama_customer END) AS supp_cust
                    , b.reff_code, (h.pcccode || ' - ' || h.pccname) AS cost_center
                FROM general_ledger_d a
                INNER JOIN general_ledger b ON b.glid = a.glid
                INNER JOIN m_coa c ON c.coaid = a.coaid
                INNER JOIN journal_type d ON d.jtid = b.jtid
                INNER JOIN person e ON e.pid = b.create_by
                LEFT JOIN m_supplier f ON b.suppid = f.suppid
                LEFT JOIN m_customer g ON b.suppid = g.custid
                LEFT JOIN profit_cost_center h ON a.pccid = h.pccid
                LEFT JOIN m_bank i ON b.other_reff_id = i.bank_id
                LEFT JOIN person j ON b.other_reff_id = j.pid
                WHERE b.glid = ? AND b.bid = ?
                ORDER BY a.gldid";
        $res = DB::Execute($sql, [$myglid, $bid]);

        return $res;
    } /*}}}*/
}
?>