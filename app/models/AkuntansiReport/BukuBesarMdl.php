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

        $addsql = $sqlOpbal = "";
        $bid = $data['bid'];
        $status_cabang = $data['status_cabang'];
        $status_coa = $data['status_coa'];
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $is_posted = $data['is_posted'];
        $coaid_from = $data['coaid_from'];
        $coaid_to = $data['coaid_to'];
        $with_bb = $data['with_bb'];
        $record = [];
        $addsql = "";
        $optionsCabang = FilterCabang($bid);

        if ($is_posted) $addsql .= " AND a.is_posted = '$is_posted'";

        $rs_coa = DB::GetOne("SELECT string_agg(coacode, ';' ORDER BY coacode) FROM m_coa WHERE coaid IN (?, ?)", [$coaid_from, $coaid_to]);

        $data_coa = explodeData(';', $rs_coa);
        $coacode_from = $data_coa[0];
        $coacode_to = $data_coa[1];

        if ($coaid_from != $coaid_to)
        {   
            $addsql .= " AND c.coacode BETWEEN '$coacode_from' AND '$coacode_to'";
            $sqlOpbal .= " AND x.coacode BETWEEN '$coacode_from' AND '$coacode_to'";
        }
        else
        {
            $addsql .= " AND c.coacode = '$coacode_from'";
            $sqlOpbal .= " AND x.coacode = '$coacode_to'";
        }

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_ledger");

        $sqli = "CREATE TEMPORARY TABLE temp_ledger (
                    branch_code VARCHAR,
                    gldate      TIMESTAMP,
                    coacode     VARCHAR,
                    coaname     VARCHAR,
                    gldesc      VARCHAR,
                    coa_vs      VARCHAR,
                    gluser      VARCHAR,
                    supp_cust   VARCHAR,
                    openingbal  NUMERIC(18,2) DEFAULT 0,
                    debet       NUMERIC(18,2) DEFAULT 0,
                    credit      NUMERIC(18,2) DEFAULT 0,
                    closingbal  NUMERIC(18,2) DEFAULT 0,
                    gldoc       VARCHAR,
                    reff_code   VARCHAR,
                    gltype      VARCHAR,
                    glnotes     VARCHAR,
                    cost_center VARCHAR
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            if ($with_bb == 't')
            {
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

            $sql = "SELECT br.branch_code, b.gldate, c.coacode, c.coaname, b.gldesc
                        , e.nama_lengkap, a.debet, a.credit
                        , format_glcode(b.gldate, d.doc_code, b.gldoc, b.glid) AS doc_no
                        , a.reff_code, d.journal_name, a.notes
                        , (g.pcccode || ' - ' || g.pccname) AS cost_center

                        -- a.gldid, b.glid, a.coaid
                        -- , c.default_debet
                        -- , (CASE WHEN b.jtid IN (27) THEN h.bank_nama ELSE f.nama_supp END) nama_supp
                        -- , g.pccid, ob.opbal
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
                    INNER JOIN branch br ON br.bid = b.bid
                    WHERE DATE(b.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                        $addsql
                    ";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'gldate'        => $rs->fields['gldate'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'gluser'        => $rs->fields['nama_lengkap'],
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'gldoc'         => $rs->fields['doc_no'],
                    'reff_code'     => $rs->fields['reff_code'],
                    'gltype'        => $rs->fields['journal_name'],
                    'glnotes'       => $rs->fields['notes'],
                    'cost_center'   => $rs->fields['cost_center'],

                    // 'openingbal'    => floatval($rs->fields['openingbal']),
                    // 'closingbal'    => floatval($rs->fields['closingbal']),
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

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