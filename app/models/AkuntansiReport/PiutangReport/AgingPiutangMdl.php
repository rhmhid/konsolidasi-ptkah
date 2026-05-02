<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class AgingPiutangMdl extends DB
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
                    FROM temp_aging_piutang
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
                    , tmp.nama_customer
                    , tmp.no_inv
                    , tmp.ardate
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
                    FROM temp_aging_piutang
                ) tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql
                ORDER BY tmp.nama_customer";
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
        DB::Execute("DROP TABLE IF EXISTS temp_aging_piutang");

        $sqli = "CREATE TEMPORARY TABLE temp_aging_piutang (
                    branch_code     VARCHAR,
                    nama_customer   VARCHAR,
                    no_inv          VARCHAR,
                    ardate          TIMESTAMP,
                    duedate         TIMESTAMP,
                    up              VARCHAR,
                    saldo           NUMERIC(18, 2) DEFAULT 0
                );";
        DB::Execute($sqli);

        DB::Execute("CREATE INDEX idx_temp_ap_cabang ON temp_aging_piutang(branch_code);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, mc.nama_customer
                        , (CASE WHEN gl.jtid IN (25, 26) THEN NULL ELSE mar.no_inv END) AS no_inv
                        , (CASE WHEN gl.jtid IN (25, 26) THEN NULL ELSE mar.ardate END) AS ardate
                        , (CASE WHEN gl.jtid IN (25, 26) THEN NULL ELSE mar.duedate END) AS duedate
                        , SUM(gld.debet - gld.credit) AS saldo
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                    INNER JOIN branch br ON gl.bid = br.bid
                    INNER JOIN m_customer mc ON mc.custid = gl.suppid
                    -- LEFT JOIN ar_customer arc ON gld.reff_id = arc.arcid AND gl.jtid IN (25, 26)
                    LEFT JOIN manual_ar mar ON gld.reff_id = mar.maid AND gl.jtid IN (27, 28)
                    WHERE gl.jtid IN (25, 26, 27, 28) AND gld.gltype = (CASE WHEN gl.jtid IN (26, 28) THEN 1 ELSE 2 END)
                        AND DATE(gl.gldate) <= '$edate' AND mc.custid NOT IN (-1, -2)
                    GROUP BY br.branch_code, mc.nama_customer
                        , (CASE WHEN gl.jtid IN (25, 26) THEN NULL ELSE mar.no_inv END)
                        , (CASE WHEN gl.jtid IN (25, 26) THEN NULL ELSE mar.ardate END)
                        , (CASE WHEN gl.jtid IN (25, 26) THEN NULL ELSE mar.duedate END)
                    HAVING SUM(gld.debet - gld.credit) <> 0";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'nama_customer' => $rs->fields['nama_customer'],
                    'no_inv'        => $rs->fields['no_inv'],
                    'ardate'        => $rs->fields['ardate'],
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
            $sql = "SELECT mc.nama_customer
                        , (CASE WHEN gl.jtid IN (25, 26) THEN arc.no_inv ELSE mar.no_inv END) AS no_inv
                        , (CASE WHEN gl.jtid IN (25, 26) THEN arc.ardate ELSE mar.ardate END) AS ardate
                        , (CASE WHEN gl.jtid IN (25, 26) THEN arc.duedate ELSE mar.duedate END) AS duedate
                        , SUM(gld.debet - gld.credit) AS saldo
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                    INNER JOIN m_customer mc ON mc.custid = gl.suppid
                    LEFT JOIN ar_customer arc ON gld.reff_id = arc.arcid AND gl.jtid IN (25, 26)
                    LEFT JOIN manual_ar mar ON gld.reff_id = mar.maid AND gl.jtid IN (27, 28)
                    WHERE gl.jtid IN (25, 26, 27, 28) AND gld.gltype = (CASE WHEN gl.jtid IN (26, 28) THEN 1 ELSE 2 END)
                        AND DATE(gl.gldate) <= '$edate' AND mc.custid NOT IN (-1, -2)
                    GROUP BY mc.nama_customer
                        , (CASE WHEN gl.jtid IN (25, 26) THEN arc.no_inv ELSE mar.no_inv END)
                        , (CASE WHEN gl.jtid IN (25, 26) THEN arc.ardate ELSE mar.ardate END)
                        , (CASE WHEN gl.jtid IN (25, 26) THEN arc.duedate ELSE mar.duedate END)
                    HAVING SUM(gld.debet - gld.credit) <> 0";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'nama_customer' => $rs->fields['nama_customer'],
                    'no_inv'        => $rs->fields['no_inv'],
                    'ardate'        => $rs->fields['ardate'],
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
                if ($row['duedate'] == '') $row['duedate'] = $row['ardate'];

                $up = DB::GetOne("SELECT (DATE('$edate') - DATE('{$row['duedate']}'))");

                $data = array(
                    'branch_code'   => $row['branch_code'],
                    'nama_customer' => $row['nama_customer'],
                    'no_inv'        => $row['no_inv'],
                    'ardate'        => $row['ardate'],
                    'duedate'       => $row['duedate'],
                    'up'            => $up,
                    'saldo'         => floatval($row['saldo'])
                );

                $sqli = "SELECT * FROM temp_aging_piutang WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */
    } /*}}}*/
}
?>