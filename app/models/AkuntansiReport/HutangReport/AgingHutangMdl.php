<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class AgingHutangMdl extends DB
{
    static $kode_kah, $kode_rsjk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        self::$kode_kah = dataConfigs('default_kode_branch_kah');

        self::$kode_rsjk = dataConfigs('default_kode_branch_rsjk');
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $addsql = "";
        $bid = $data['bid'];
        $status_cabang = $data['status_cabang'];
        $optionsCabang = FilterCabang($bid);
        $data['optionsCabang'] = $optionsCabang;

        self::_DataSummary($data);

        if ($status_cabang) $addsql .= " AND br.is_aktif = '$status_cabang'";

        $addsql .= $optionsCabang['query'];

        /* B: Showing Data From Temp Table */
        $sql = "SELECT br.bid
                    , tmp.branch_code
                    , br.branch_name
                    , SUM(tmp.saldo) AS saldo
                    , SUM(CASE WHEN tmp.umur <= 0 THEN tmp.saldo ELSE 0 END) AS up0
                    , SUM(CASE WHEN tmp.umur > 0 AND tmp.umur <= 30 THEN tmp.saldo ELSE 0 END) AS up1
                    , SUM(CASE WHEN tmp.umur >= 31 AND tmp.umur <= 60 THEN tmp.saldo ELSE 0 END) AS up2
                    , SUM(CASE WHEN tmp.umur >= 61 AND tmp.umur <= 90 THEN tmp.saldo ELSE 0 END) AS up3
                    , SUM(CASE WHEN tmp.umur > 90 THEN tmp.saldo ELSE 0 END) AS up4
                FROM (
                    SELECT *,
                        CAST(
                            CASE 
                                WHEN COALESCE(up, '00:00:00') = '00:00:00' THEN '0'
                                ELSE REPLACE(up, ' days', '')
                            END AS INTEGER
                        ) AS umur
                    FROM temp_aging_hutang
                ) tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql
                GROUP BY tmp.branch_code, br.bid, br.is_primary, br.branch_name
                ORDER BY br.is_primary DESC, br.branch_name";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    public static function detail ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $addsql = "";
        $bid = $data['bid'];
        $status_cabang = $data['status_cabang'];
        $optionsCabang = FilterCabang($bid);
        $data['optionsCabang'] = $optionsCabang;

        self::_DataSummary($data);

        if ($status_cabang) $addsql .= " AND br.is_aktif = '$status_cabang'";

        $addsql .= $optionsCabang['query'];

        /* B: Showing Data From Temp Table */
        $sql = "SELECT br.bid
                    , tmp.branch_code
                    , br.branch_name
                    , tmp.nama_supp
                    , tmp.no_inv
                    , tmp.apdate
                    , tmp.duedate
                    , tmp.saldo
                    , (CASE WHEN tmp.umur <= 0 THEN tmp.saldo ELSE 0 END) AS up0
                    , (CASE WHEN tmp.umur > 0 AND tmp.umur <= 30 THEN tmp.saldo ELSE 0 END) AS up1
                    , (CASE WHEN tmp.umur >= 31 AND tmp.umur <= 60 THEN tmp.saldo ELSE 0 END) AS up2
                    , (CASE WHEN tmp.umur >= 61 AND tmp.umur <= 90 THEN tmp.saldo ELSE 0 END) AS up3
                    , (CASE WHEN tmp.umur > 90 THEN tmp.saldo ELSE 0 END) AS up4
                FROM (
                    SELECT *,
                        CAST(
                            CASE 
                                WHEN COALESCE(up, '00:00:00') = '00:00:00' THEN '0'
                                ELSE REPLACE(up, ' days', '')
                            END AS INTEGER
                        ) AS umur
                    FROM temp_aging_hutang
                ) tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql
                ORDER BY tmp.nama_supp";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    private static function _DataSummary ($data) /*{{{*/
    {
        $bid = $data['bid'];
        $month = $data['month'];
        $year = $data['year'];
        $sdate = date('Y-m-d', strtotime($data['year'].'-'.$data['month'].'-01'));
        $edate = date('Y-m-t', strtotime($sdate));
        $record = [];
        $optionsCabang = $data['optionsCabang'];

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_aging_hutang");

        $sqli = "CREATE TEMPORARY TABLE temp_aging_hutang (
                    branch_code VARCHAR,
                    nama_supp   VARCHAR,
                    no_inv      VARCHAR,
                    apdate      TIMESTAMP,
                    duedate     TIMESTAMP,
                    up          VARCHAR,
                    saldo       NUMERIC(18, 2) DEFAULT 0
                );";
        DB::Execute($sqli);

        DB::Execute("CREATE INDEX idx_temp_ap_cabang ON temp_aging_hutang(branch_code);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, ms.nama_supp
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.no_invoice ELSE map.no_inv END) AS no_inv
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.apdate ELSE map.apdate END) AS apdate
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.duedate ELSE map.duedate END) AS duedate
                        , (DATE('$edate') - DATE((CASE WHEN gl.jtid IN (20, 21) THEN aps.duedate ELSE map.duedate END))) AS up
                        , SUM(gld.credit - gld.debet) AS saldo
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                    INNER JOIN branch br ON gl.bid = br.bid
                    INNER JOIN m_supplier ms ON ms.suppid = gl.suppid
                    LEFT JOIN ap_supplier aps ON gld.reff_id = aps.apsid AND gl.jtid IN (20, 21)
                    LEFT JOIN manual_ap map ON gld.reff_id = map.maid AND gl.jtid IN (22, 23)
                    WHERE gl.jtid IN (20, 21, 22, 23) AND gld.gltype = (CASE WHEN gl.jtid IN (21, 23) THEN 1 ELSE 2 END)
                        AND DATE(gl.gldate) <= '$edate'
                    GROUP BY br.branch_code, ms.nama_supp, up
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.no_invoice ELSE map.no_inv END)
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.apdate ELSE map.apdate END)
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.duedate ELSE map.duedate END)
                    HAVING SUM(gld.credit - gld.debet) <> 0";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'nama_supp'     => $rs->fields['nama_supp'],
                    'no_inv'        => $rs->fields['no_inv'],
                    'apdate'        => $rs->fields['apdate'],
                    'duedate'       => $rs->fields['duedate'],
                    'saldo'         => floatval($rs->fields['saldo'])
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT ms.nama_supp
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.no_invoice ELSE map.no_inv END) AS no_inv
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.apdate ELSE map.apdate END) AS apdate
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.duedate ELSE map.duedate END) AS duedate
                        , (DATE('$edate') - DATE((CASE WHEN gl.jtid IN (20, 21) THEN aps.duedate ELSE map.duedate END))) AS up
                        , SUM(gld.credit - gld.debet) AS saldo
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                    INNER JOIN m_supplier ms ON ms.suppid = gl.suppid
                    LEFT JOIN ap_supplier aps ON gld.reff_id = aps.apsid AND gl.jtid IN (20, 21)
                    LEFT JOIN manual_ap map ON gld.reff_id = map.maid AND gl.jtid IN (22, 23)
                    WHERE gl.jtid IN (20, 21, 22, 23) AND gld.gltype = (CASE WHEN gl.jtid IN (21, 23) THEN 1 ELSE 2 END)
                        AND DATE(gl.gldate) <= '$edate'
                    GROUP BY ms.nama_supp, up
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.no_invoice ELSE map.no_inv END)
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.apdate ELSE map.apdate END)
                        , (CASE WHEN gl.jtid IN (20, 21) THEN aps.duedate ELSE map.duedate END)
                    HAVING SUM(gld.credit - gld.debet) <> 0";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'nama_supp'     => $rs->fields['nama_supp'],
                    'no_inv'        => $rs->fields['no_inv'],
                    'apdate'        => $rs->fields['apdate'],
                    'duedate'       => $rs->fields['duedate'],
                    'saldo'         => floatval($rs->fields['saldo'])
                );

                $rs->MoveNext();
            }
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
                $up = DB::GetOne("SELECT (DATE('$edate') - DATE('{$row['duedate']}'))");

                $data = array(
                    'branch_code'   => $row['branch_code'],
                    'nama_supp'     => $row['nama_supp'],
                    'no_inv'        => $row['no_inv'],
                    'apdate'        => $row['apdate'],
                    'duedate'       => $row['duedate'],
                    'up'            => $up,
                    'saldo'         => floatval($row['saldo'])
                );

                $sqli = "SELECT * FROM temp_aging_hutang WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */
    } /*}}}*/
}
?>