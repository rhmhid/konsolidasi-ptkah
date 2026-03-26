<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class LabaRugiMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = $data['bid'];
        $smonth = $data['prev_month'];
        $syear = $data['prev_year'];
        $emonth = $data['month'];
        $eyear = $data['year'];
        $paid = $data['paid'];
        $pbegin = $data['pbegin'];
        $pend = $data['pend'];
        $status_cabang = $data['status_cabang'];
        $status_coa = $data['status_coa'];
        $record = [];
        $addsql = "";

        $opbal = 'a.openingbal';
        for ($i = 1; $i <= $emonth; $i++)
            $opbal .= ' + a.amount'.$i;

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_profit_loss");

        $sqli = "CREATE TEMPORARY TABLE temp_profit_loss (
                    branch_code   VARCHAR,
                    coaid         INT,
                    coatype       VARCHAR,
                    coatid        INT,
                    coacode       VARCHAR,
                    coaname       VARCHAR,
                    default_debet BOOLEAN,
                    openingbal    NUMERIC(18,2) DEFAULT 0,
                    debet         NUMERIC(18,2) DEFAULT 0,
                    credit        NUMERIC(18,2) DEFAULT 0,
                    closingbal    NUMERIC(18,2) DEFAULT 0,
                    pplid         INT,
                    pplrid        INT
                ) ON COMMIT PRESERVE ROWS;";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        $sql = "SELECT e.branch_code, c.coatype, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END) AS coatid
                    , b.coacode, b.coaname, b.default_debet, (b.coacode || ' ' || b.coaname) AS mycoa, COALESCE(b.pplid, 0) AS pplid
                    , COALESCE(a.amount{$smonth}, 0) AS amount_bln_prev, COALESCE(a.amount{$emonth}, 0) AS amount_bln
                    , ({$opbal}) AS closingbal, COALESCE(f.pplrid, 0) AS pplrid
                FROM ledger_summary a
                JOIN m_coa b ON b.coaid = a.coaid
                JOIN m_coatype c ON c.coatid = b.coatid
                JOIN periode_akunting d ON d.paid = a.paid
                INNER JOIN branch e ON e.bid = a.bid
                LEFT JOIN pos_pl f ON b.pplid = f.pplid
                WHERE b.coatid > 3 AND a.paid = ?";
        $rs = DB2::Execute($sql, [$paid]);

        while (!$rs->EOF)
        {
            $record[] = array(
                'branch_code'   => $rs->fields['branch_code'],
                'coaid'         => $rs->fields['coaid'],
                'coatype'       => $rs->fields['coatype'],
                'coatid'        => $rs->fields['coatid'],
                'coacode'       => $rs->fields['coacode'],
                'coaname'       => $rs->fields['coaname'],
                'default_debet' => $rs->fields['default_debet'],
                'openingbal'    => floatval($rs->fields['openingbal']),
                'debet'         => floatval($rs->fields['debet']),
                'credit'        => floatval($rs->fields['credit']),
                'closingbal'    => floatval($rs->fields['closingbal']),
                'pplid'         => $rs->fields['pplid'],
                'pplrid'        => $rs->fields['pplrid'],
            );

            $rs->MoveNext();
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        $sql = "SELECT c.coatype, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END) AS coatid
                    , b.coacode, b.coaname, b.default_debet, (b.coacode || ' ' || b.coaname) AS mycoa, COALESCE(b.pplid, 0) AS pplid
                    , COALESCE(a.amount{$smonth}, 0) AS amount_bln_prev, COALESCE(a.amount{$emonth}, 0) AS amount_bln
                    , ({$opbal}) AS closingbal, COALESCE(f.pplrid, 0) AS pplrid
                FROM ledger_summary a
                JOIN m_coa b ON b.coaid = a.coaid
                JOIN m_coatype c ON c.coatid = b.coatid
                JOIN periode_akunting d ON d.paid = a.paid
                LEFT JOIN pos_pl f ON b.pplid = f.pplid
                WHERE b.coatid > 3 AND a.paid = ?";
        $rs = DB3::Execute($sql, [$paid]);

        while (!$rs->EOF)
        {
            $record[] = array(
                'branch_code'   => dataConfigs('default_kode_branch_kah'),
                'coaid'         => $rs->fields['coaid'],
                'coatype'       => $rs->fields['coatype'],
                'coatid'        => $rs->fields['coatid'],
                'coacode'       => $rs->fields['coacode'],
                'coaname'       => $rs->fields['coaname'],
                'default_debet' => $rs->fields['default_debet'],
                'openingbal'    => floatval($rs->fields['openingbal']),
                'debet'         => floatval($rs->fields['debet']),
                'credit'        => floatval($rs->fields['credit']),
                'closingbal'    => floatval($rs->fields['closingbal']),
                'pplid'         => $rs->fields['pplid'],
                'pplrid'        => $rs->fields['pplrid'],
            );

            $rs->MoveNext();
        }
        /* E: Get Data PT. KAH */

        /* B: Insert To Temp Table */
        $ok = true;
        if (!empty($record))
        {
            foreach ($record as $idx => $row)
            {
                $data = array(
                    'branch_code'   => $row['branch_code'],
                    'coaid'         => $row['coaid'],
                    'coatype'       => $row['coatype'],
                    'coatid'        => $row['coatid'],
                    'coacode'       => $row['coacode'],
                    'coaname'       => $row['coaname'],
                    'default_debet' => $row['default_debet'],
                    'openingbal'    => floatval($row['openingbal']),
                    'debet'         => floatval($row['debet']),
                    'credit'        => floatval($row['credit']),
                    'closingbal'    => floatval($row['closingbal']),
                    'pplid'         => $row['pplid'],
                    'pplrid'        => $row['pplrid'],
                );

                $sqli = "SELECT * FROM temp_profit_loss WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */

        if ($status_cabang) $addsql .= " AND br.is_aktif = '$status_cabang'";

        if ($status_coa) $addsql .= " AND mc.is_valid = '$status_coa'";

        /* B: Showing Data From Temp Table */
        $sql = "SELECT b.*, tmp.branch_code, tmp.openingbal, tmp.debet, tmp.credit, tmp.closingbal
                FROM temp_profit_loss tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, mc.coaid, mct.coatype, mc.coatid, mc.coacode, mc.coaname, mc.default_debet
                        , (mc.coacode || ' ' || mc.coaname) AS mycoa, mcb.coacode_from, mcb.coacode_to
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    WHERE mc.allow_post = 't' $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                ORDER BY (CASE WHEN tmp.coatid = 5 AND tmp.default_debet = 'f' THEN 4 ELSE tmp.coatid END), tmp.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_daily ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $pmonth = $data['pmonth'];

        $sql = "SELECT c.coatype, gld.coaid, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END) AS coatid
                    , b.coacode, b.coaname, b.default_debet, (b.coacode || ' ' || b.coaname) AS mycoa, COALESCE(b.pplid, 0) AS pplid
                    , SUM(gld.credit - gld.debet) AS amount_untill
                    , SUM(CASE WHEN DATE(gl.gldate) BETWEEN '$sdate' AND '$edate' THEN gld.credit - gld.debet ELSE 0 END) AS amount_period
                    , COALESCE(e.pplrid, 0) AS pplrid
                FROM general_ledger gl
                JOIN general_ledger_d gld ON gl.glid = gld.glid
                JOIN m_coa b ON b.coaid = gld.coaid
                JOIN m_coatype c ON c.coatid = b.coatid
                LEFT JOIN pos_pl e ON b.pplid = e.pplid
                WHERE b.coatid > 3 AND DATE(gl.gldate) BETWEEN DATE('$pmonth') AND DATE('$edate') AND gl.bid = ?
                GROUP BY c.coatype, gld.coaid, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END)
                    , b.coacode, b.coaname, b.default_debet, mycoa, COALESCE(b.pplid, 0), COALESCE(e.pplrid, 0)
                ORDER BY (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END), b.coacode";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/

    public static function list_pos_rekap () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_pl_rekap WHERE is_aktif = 't' ORDER BY urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_pl WHERE is_aktif = 't' ORDER BY urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>