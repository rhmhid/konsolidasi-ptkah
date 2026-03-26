<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class NeracaMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = $data['bid'];
        $month = $data['month'];
        $year = $data['year'];
        $paid = $data['paid'];
        $pbegin = $data['pbegin'];
        $pend = $data['pend'];
        $status_cabang = $data['status_cabang'];
        $status_coa = $data['status_coa'];
        $record = [];
        $addsql = "";

        $opbal = 'a.openingbal';
        for ($i = 1; $i < $month; $i++)
            $opbal .= ' + a.amount'.$i;

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_balance_sheet");

        $sqli = "CREATE TEMPORARY TABLE temp_balance_sheet (
                    branch_code     VARCHAR,
                    coaid           INT,
                    coatype         VARCHAR,
                    coatid          INT,
                    coacode         VARCHAR,
                    coaname         VARCHAR,
                    default_debet   BOOLEAN,
                    openingbal      NUMERIC(18,2) DEFAULT 0,
                    closingbal      NUMERIC(18,2) DEFAULT 0,
                    pnid            INT
                ) ON COMMIT PRESERVE ROWS;";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        $sql = "SELECT e.branch_code, c.coatid, UPPER(c.coatype) AS coatype, b.coaid, b.coacode, b.coaname, b.default_debet, COALESCE(b.pnid, 0) AS pnid
                    , (CASE WHEN b.coaid IN (".Modules::$laba_periode_lalu.", ".Modules::$laba_periode_berjalan.") THEN
                        0
                    ELSE
                        COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0)
                    END) AS openingbal
                    , (CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE ({$opbal} + a.amount{$month}) END) AS closingbal
                FROM ledger_summary a
                INNER JOIN m_coa b ON b.coaid = a.coaid
                INNER JOIN m_coatype c ON c.coatid = b.coatid
                INNER JOIN periode_akunting d ON d.paid = a.paid
                INNER JOIN branch e ON e.bid = a.bid
                LEFT JOIN pos_neraca f ON b.pnid = f.pnid
                WHERE d.pend = (SELECT MAX(d.pend)
                        FROM ledger_summary e, periode_akunting d
                        WHERE d.paid = e.paid AND e.coaid = a.coaid AND d.pend <= DATE('{$pend}'))
                    AND b.coatid <= 3";
        $rs = DB2::Execute($sql);

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
                'closingbal'    => floatval($rs->fields['closingbal']),
                'pnid'          => $rs->fields['pnid']
            );

            $rs->MoveNext();
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        $sql = "SELECT c.coatid, UPPER(c.coatype) AS coatype, b.coaid, b.coacode, b.coaname, b.default_debet, COALESCE(b.pnid, 0) AS pnid
                    , (CASE WHEN b.coaid IN (".Modules::$laba_periode_lalu.", ".Modules::$laba_periode_berjalan.") THEN
                        0
                    ELSE
                        COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0)
                    END) AS openingbal
                    , (CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE ({$opbal} + a.amount{$month}) END) AS closingbal
                FROM ledger_summary a
                INNER JOIN m_coa b ON b.coaid = a.coaid
                INNER JOIN m_coatype c ON c.coatid = b.coatid
                INNER JOIN periode_akunting d ON d.paid = a.paid
                LEFT JOIN pos_neraca f ON b.pnid = f.pnid
                WHERE d.pend = (SELECT MAX(d.pend)
                        FROM ledger_summary e, periode_akunting d
                        WHERE d.paid = e.paid AND e.coaid = a.coaid AND d.pend <= DATE('{$pend}'))
                    AND b.coatid <= 3";
        $rs = DB3::Execute($sql);

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
                'closingbal'    => floatval($rs->fields['closingbal']),
                'pnid'          => $rs->fields['pnid']
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
                    'closingbal'    => floatval($row['closingbal']),
                    'pnid'          => $row['pnid']
                );

                $sqli = "SELECT * FROM temp_balance_sheet WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */

        if ($bid) $addsql .= " AND br.bid = ".$bid;

        if ($status_cabang) $addsql .= " AND br.is_aktif = '$status_cabang'";

        if ($status_coa) $addsql .= " AND mc.is_valid = '$status_coa'";

        /* B: Showing Data From Temp Table */
        $sql = "SELECT b.*, tmp.branch_code, tmp.openingbal, tmp.closingbal
                FROM temp_balance_sheet tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, mc.coaid, mct.coatype, mc.coatid, mc.coacode, mc.coaname, mc.default_debet
                        , (mc.coacode || ' ' || mc.coaname) AS mycoa, mcb.coacode_from, mcb.coacode_to
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    WHERE mc.allow_post = 't' $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                ORDER BY b.coatid, b.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_neraca WHERE is_aktif = 't' ORDER BY urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>