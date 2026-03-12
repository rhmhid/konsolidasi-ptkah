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
        if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $month = $data['month'];
        $year = $data['year'];
        $paid = $data['paid'];
        $pbegin = $data['pbegin'];
        $pend = $data['pend'];

        $opbal = 'a.openingbal';
        for ($i = 1; $i < $month; $i++)
            $opbal .= ' + a.amount'.$i;

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
                    mycoa         TEXT
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);

        $sql = "SELECT e.branch_code, a.coaid, c.coatype, b.coatid, b.coacode, b.coaname, b.default_debet
                    , COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0) AS openingbal
                    , COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_debet END), 0) AS debet
                    , COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_credit END), 0) AS credit
                    , (b.coacode || ' ' || b.coaname) AS mycoa
                FROM ledger_summary a
                INNER JOIN m_coa b ON b.coaid = a.coaid
                INNER JOIN m_coatype c ON c.coatid = b.coatid
                INNER JOIN periode_akunting d ON d.paid = a.paid
                INNER JOIN branch e ON e.bid = e.bid
                WHERE d.pend = (
                        SELECT MAX(d.pend)
                        FROM ledger_summary e, periode_akunting d
                        WHERE d.paid = e.paid AND e.coaid = a.coaid AND d.pend <= DATE('{$pend}')
                    )";
        $rs = DB2::Execute($sql);

        $ok = true;
        while (!$rs->EOF)
        {
            $record = array(
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

            $sqli = "SELECT * FROM temp_neraca_saldo WHERE 1 = 2";
            $rsi = DB::Execute($sqli);
            $sqli = DB::InsertSQL($rsi, $record);
            if ($ok) $ok = DB::Execute($sqli);

            $rs->MoveNext();
        }

        $sql = "SELECT tmp.*
                FROM temp_neraca_saldo tmp
                ORDER BY tmp.coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>