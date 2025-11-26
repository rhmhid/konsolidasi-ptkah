<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class BukuBesarMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = $sqlOpbal = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $jtid = $data['jtid'];
        $is_posted = $data['is_posted'];
        $coaid_from = $data['coaid_from'];
        $coaid_to = $data['coaid_to'];
        $pccid = $data['pccid'];
        $with_bb = $data['with_bb'];

        if ($jtid) $addsql .= " AND a.jtid = ".$jtid;

        if ($is_posted) $addsql .= " AND a.is_posted = '$is_posted'";

        if ($coaid_from != $coaid_to)
        {
            $rs_coa = DB::GetOne("SELECT string_agg(coacode, ';' ORDER BY coacode) FROM m_coa WHERE coaid IN (?, ?)", [$coaid_from, $coaid_to]);

            $data_coa = explodeData(';', $rs_coa);
            $coacode_from = $data_coa[0];
            $coacode_to = $data_coa[1];
            
            $addsql .= " AND c.coacode BETWEEN '$coacode_from' AND '$coacode_to'";
            $sqlOpbal .= " AND x.coacode BETWEEN '$coacode_from' AND '$coacode_to'";
        }
        else
        {
            $addsql .= " AND a.coaid = ".$coaid_from;
            $sqlOpbal .= " AND x.coaid = ".$coaid_from;
        }

        if ($pccid) $addsql .= " AND a.pccid = ".$pccid;

        $addsql .= " AND b.bid = ".$bid;

        if ($with_bb == 't')
        {
            $sqlOpbal .= " AND z.bid = ".$bid;

            $opbal = "SELECT x.coaid
                        , SUM(CASE WHEN x.default_debet = 't' THEN
                            y.debet - y.credit
                        ELSE
                            y.credit - y.debet
                        END
                        ) AS opbal
                    FROM general_ledger_d y
                    JOIN general_ledger z ON z.glid = y.glid
                    JOIN m_coa x ON x.coaid = y.coaid
                    WHERE z.is_posted = 't' AND DATE(z.gldate) < DATE('$sdate')
                        $sqlOpbal
                    GROUP BY x.coaid";
        }
        else
            $opbal = "SELECT x.coaid, 0 AS opbal
                    FROM m_coa x
                    WHERE 1 = 1 $sqlOpbal";

        $sql = "SELECT a.gldid, b.glid, b.gldate, a.coaid, c.coacode, c.coaname, d.journal_name
                    , a.reff_code, format_glcode(b.gldate, d.doc_code, b.gldoc, b.glid) AS gldoc
                    , c.default_debet, a.debet, a.credit, a.notes, b.gldesc, e.nama_lengkap
        		    , (CASE WHEN b.jtid IN (27) THEN h.bank_nama ELSE f.nama_supp END) nama_supp
                    , g.pccid, (g.pcccode || ' - ' || g.pccname) AS cost_center, ob.opbal
                FROM general_ledger_d a
                INNER JOIN general_ledger b ON b.glid = a.glid
                INNER JOIN m_coa c ON c.coaid = a.coaid
                INNER JOIN journal_type d ON d.jtid = b.jtid
                INNER JOIN person e ON e.pid = b.create_by
                LEFT JOIN m_supplier f ON b.suppid = f.suppid -- AND b.jtid IN (4, 9, 20, 21, 22, 23, 24)
                LEFT JOIN profit_cost_center g ON a.pccid = g.pccid
                LEFT JOIN (
                    $opbal
        		) ob ON a.coaid = ob.coaid
        		LEFT JOIN m_bank h ON b.other_reff_id = h.bank_id
                WHERE DATE(b.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY date(b.gldate) asc,b.gldate, d.doc_code, b.gldoc, b.glid
                    -- format_glcode(b.gldate, d.doc_code, b.gldoc, b.glid) ASC, c.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function detail_jurnal ($myglid, $mygldid) /*{{{*/
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
                WHERE b.glid = ? AND a.gldid != ? AND b.bid = ?
                ORDER BY a.gldid";
        $res = DB::Execute($sql, [$myglid, $mygldid, $bid]);

        return $res;
    } /*}}}*/
}
?>