<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class NeracaSaldoMdl extends DB
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
        $optionsCabang = FilterCabang($bid);

        $opbal = 'a.openingbal';
        for ($i = 1; $i < $month; $i++)
            $opbal .= ' + a.amount'.$i;

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_neraca_saldo");

        $sqli = "CREATE TEMPORARY TABLE temp_neraca_saldo (
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
                    closingbal    NUMERIC(18,2) DEFAULT 0
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $paid = DB2::GetOne("SELECT * FROM periode_akunting WHERE DATE('$year-$month-1') BETWEEN pbegin AND pend ORDER BY pbegin DESC");

            $sql = "SELECT e.branch_code, a.coaid, c.coatype, b.coatid, b.coacode, b.coaname, b.default_debet
                        , COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0) AS openingbal
                        , COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_debet END), 0) AS debet
                        , COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_credit END), 0) AS credit
                        , (b.coacode || ' ' || b.coaname) AS mycoa
                    FROM ledger_summary a
                    INNER JOIN m_coa b ON b.coaid = a.coaid
                    INNER JOIN m_coatype c ON c.coatid = b.coatid
                    INNER JOIN periode_akunting d ON d.paid = a.paid
                    INNER JOIN branch e ON e.bid = a.bid
                    WHERE d.pend = (
                            SELECT MAX(d.pend)
                            FROM ledger_summary e, periode_akunting d
                            WHERE d.paid = e.paid AND e.coaid = a.coaid AND e.bid = a.bid AND d.pend <= DATE('{$pend}')
                        )";
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
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'closingbal'    => floatval($rs->fields['closingbal']),
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $paid = DB3::GetOne("SELECT * FROM periode_akunting WHERE DATE('$year-$month-1') BETWEEN pbegin AND pend ORDER BY pbegin DESC");

            $sql = "SELECT a.coaid, c.coatype, b.coatid, b.coacode, b.coaname, b.default_debet
                        , COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0) AS openingbal
                        , COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_debet END), 0) AS debet
                        , COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_credit END), 0) AS credit
                        , (b.coacode || ' ' || b.coaname) AS mycoa
                    FROM ledger_summary a
                    INNER JOIN m_coa b ON b.coaid = a.coaid
                    INNER JOIN m_coatype c ON c.coatid = b.coatid
                    INNER JOIN periode_akunting d ON d.paid = a.paid
                    WHERE d.pend = (
                            SELECT MAX(d.pend)
                            FROM ledger_summary e, periode_akunting d
                            WHERE d.paid = e.paid AND e.coaid = a.coaid AND d.pend <= DATE('{$pend}')
                        )";
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
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'closingbal'    => floatval($rs->fields['closingbal']),
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. KAH */

        /* B: Get Data RSJK */
        if ($optionsCabang['conn_rsjk'])
        {
            $rsjk_code = dataConfigs('default_kode_branch_rsjk');

            $endpoint = 'pass/trial_balance';
            $payload = [
                'data' => [
                    'month' => $month,
                    'year'  => $year
                ]
            ];

            $response = Bridging::post($rsjk_code, $endpoint, $payload);

            if ($response['status'] === 'success' && !empty($response['data']))
            {
                foreach ($response['data'] as $row)
                {
                    $default_debet = $row['posisi_coa'] == 'Dr' ? 't' : 'f';

                    $record[] = array(
                        'branch_code'   => $rsjk_code,
                        'coacode'       => $row['coa_id'], 
                        'coaname'       => $row['coa_name'],
                        'default_debet' => $default_debet,
                        'openingbal'    => floatval($row['openingbal']),
                        'debet'         => floatval($row['debet']),
                        'credit'        => floatval($row['credit']),
                        'closingbal'    => floatval($row['closingbal']),
                    );
                }
            }
        }
        /* E: Get Data RSJK */

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
                );

                $sqli = "SELECT * FROM temp_neraca_saldo WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */

        if ($status_cabang) $addsql .= " AND br.is_aktif = '$status_cabang'";

        if ($status_coa) $addsql .= " AND mc.is_valid = '$status_coa'";

        $addsql .= $optionsCabang['query'];

        /* B: Showing Data From Temp Table */
        $sql = "SELECT b.*, tmp.branch_code, tmp.openingbal, tmp.debet, tmp.credit, tmp.closingbal
                FROM temp_neraca_saldo tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, br.kdbid, mc.coaid, mct.coatype, mc.coatid, mc.coacode
                        , mc.coaname, mc.default_debet, (mc.coacode || ' ' || mc.coaname) AS mycoa, mc.pnid
                        , mcb.coacode_from, mcb.coacode_to
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    WHERE mc.allow_post = 't' $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                ORDER BY tmp.coacode";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/
}
?>