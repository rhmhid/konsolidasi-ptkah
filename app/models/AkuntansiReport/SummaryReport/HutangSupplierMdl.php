<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class HutangSupplierMdl extends DB
{
    static $kode_kah, $kode_rsjk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        self::$kode_kah = dataConfigs('default_kode_branch_kah');

        self::$kode_rsjk = dataConfigs('default_kode_branch_rsjk');
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = $addsql2 = "";
        $bid = $data['bid'];
        $status_cabang = $data['status_cabang'];
        $month = $data['month'];
        $year = $data['year'];
        $sdate = date('Y-m-d', strtotime($data['year'].'-'.$data['month'].'-01'));
        $edate = date('Y-m-t', strtotime($sdate));
        $record = [];
        $optionsCabang = FilterCabang($bid);

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_summary_ap");

        $sqli = "CREATE TEMPORARY TABLE temp_summary_ap (
                    branch_code VARCHAR,
                    bebal NUMERIC(18, 2) DEFAULT 0,
                    ap_inv NUMERIC(18, 2) DEFAULT 0,
                    ap_pay NUMERIC(18, 2) DEFAULT 0,
                    opbal NUMERIC(18, 2) DEFAULT 0
                );";
        DB::Execute($sqli);

        DB::Execute("CREATE INDEX idx_temp_ap_cabang ON temp_summary_ap(branch_code);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code
                        , SUM(CASE WHEN DATE(gl.gldate) < '$edate' THEN gld.credit - gld.debet END) AS bebal
                        , SUM(CASE WHEN DATE(gl.gldate) BETWEEN '$sdate' AND '$edate' AND gl.jtid IN (20, 22) THEN (gld.credit - gld.debet) END) AS ap_inv
                        , SUM(CASE WHEN DATE(gl.gldate) BETWEEN '$sdate' AND '$edate' AND gl.jtid IN (21, 23) THEN (gld.debet - gld.credit) END) AS ap_pay
                        , SUM(gld.credit - gld.debet) AS opbal
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                    INNER JOIN branch br ON gl.bid = br.bid
                    INNER JOIN m_supplier ms ON ms.suppid = gl.suppid
                    WHERE gl.jtid IN (20, 21, 22, 23) AND gld.gltype = (CASE WHEN gl.jtid IN (21, 23) THEN 1 ELSE 2 END)
                        AND DATE(gl.gldate) <= '$edate' AND ms.suppid NOT IN (-1)
                    GROUP BY br.branch_code";
            echo "<br /><br /><br /><br /><br />";myprint_r($sql);
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'bebal'         => floatval($rs->fields['bebal']),
                    'ap_inv'        => floatval($rs->fields['ap_inv']),
                    'ap_pay'        => floatval($rs->fields['ap_pay']),
                    'opbal'         => floatval($rs->fields['opbal']),
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            // $sql = "SELECT a.glid, a.gldate, a.gldesc, c.nama_lengkap AS useri
            //             , format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) AS gldoc
            //             , b.journal_name, a.is_posted
            //         FROM general_ledger a
            //         INNER JOIN journal_type b ON b.jtid = a.jtid
            //         INNER JOIN person c ON c.pid = a.create_by
            //         WHERE DATE(a.gldate) BETWEEN DATE('$jurnal_speriod') AND DATE('$jurnal_eperiod')
            //             $addsql";
            // $rs = DB3::Execute($sql);

            // while (!$rs->EOF)
            // {
            //     $record[] = array(
            //         'branch_code'   => self::$kode_kah,
            //         'glid'          => $rs->fields['glid'],
            //         'gldate'        => $rs->fields['gldate'],
            //         'gldesc'        => $rs->fields['gldesc'],
            //         'useri'         => $rs->fields['useri'],
            //         'gldoc'         => $rs->fields['gldoc'],
            //         'journal_name'  => $rs->fields['journal_name'],
            //         'is_posted'     => $rs->fields['is_posted']
            //     );

            //     $rs->MoveNext();
            // }
        }
        /* E: Get Data PT. KAH */

        /* B: Get Data RSJK */
        if ($optionsCabang['conn_rsjk'])
        {
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
                    'bebal'         => floatval($row['bebal']),
                    'ap_inv'        => floatval($row['ap_inv']),
                    'ap_pay'        => floatval($row['ap_pay']),
                    'opbal'         => floatval($row['opbal']),
                );

                $sqli = "SELECT * FROM temp_summary_ap WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */

        if ($status_cabang) $addsql2 .= " AND br.is_aktif = '$status_cabang'";

        $addsql2 .= $optionsCabang['query'];

        /* B: Showing Data From Temp Table */
        $sql = "SELECT tmp.*, br.bid, br.branch_name
                FROM temp_summary_ap tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql2
                ORDER BY br.is_primary DESC, br.branch_name";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/
}
?>