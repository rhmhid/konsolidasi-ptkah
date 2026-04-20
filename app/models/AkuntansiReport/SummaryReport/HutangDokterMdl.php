<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class HutangDokterMdl extends DB
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
        $sql = "SELECT tmp.branch_code, SUM(tmp.opbal) AS opbal, SUM(tmp.ap_inv) AS ap_inv
                    , SUM(tmp.ap_pay) AS ap_pay, SUM(tmp.closbal) AS closbal
                    , br.bid, br.branch_name
                FROM temp_summary_ap tmp
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
        $sql = "SELECT tmp.branch_code, tmp.nama_dokter, SUM(tmp.opbal) AS opbal
                    , SUM(tmp.ap_inv) AS ap_inv, SUM(tmp.ap_pay) AS ap_pay
                    , SUM(tmp.closbal) AS closbal, br.bid, br.branch_name
                FROM temp_summary_ap tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql
                GROUP BY tmp.branch_code, tmp.nama_dokter, br.bid, br.is_primary, br.branch_name
                ORDER BY tmp.nama_dokter";
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
        DB::Execute("DROP TABLE IF EXISTS temp_summary_ap");

        $sqli = "CREATE TEMPORARY TABLE temp_summary_ap (
                    branch_code VARCHAR,
                    nama_dokter VARCHAR,
                    opbal NUMERIC(18, 2) DEFAULT 0,
                    ap_inv NUMERIC(18, 2) DEFAULT 0,
                    ap_pay NUMERIC(18, 2) DEFAULT 0,
                    closbal NUMERIC(18, 2) DEFAULT 0
                );";
        DB::Execute($sqli);

        DB::Execute("CREATE INDEX idx_temp_ap_cabang ON temp_summary_ap(branch_code);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, pd.nama_lengkap AS nama_dokter
                        , SUM(CASE WHEN DATE(gl.gldate) < '$edate' THEN gld.credit - gld.debet ELSE 0 END) AS opbal
                        , SUM(CASE WHEN DATE(gl.gldate) BETWEEN '$sdate' AND '$edate' AND gl.jtid IN (22) THEN (gld.credit - gld.debet) ELSE 0 END) AS ap_inv
                        , SUM(CASE WHEN DATE(gl.gldate) BETWEEN '$sdate' AND '$edate' AND gl.jtid IN (23) THEN (gld.debet - gld.credit) ELSE 0 END) AS ap_pay
                        , SUM(gld.credit - gld.debet) AS closbal
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                    INNER JOIN branch br ON gl.bid = br.bid
                    INNER JOIN m_supplier ms ON ms.suppid = gl.suppid
                    INNER JOIN person pd ON pd.pid = gl.other_reff_id
                    WHERE gl.jtid IN (22, 23) AND gld.gltype = (CASE WHEN gl.jtid IN (23) THEN 1 ELSE 2 END)
                        AND DATE(gl.gldate) <= '$edate' AND ms.suppid IN (-1)
                    GROUP BY br.branch_code, pd.nama_lengkap
                    HAVING SUM(gld.credit - gld.debet) <> 0";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'nama_dokter'     => $rs->fields['nama_dokter'],
                    'opbal'         => floatval($rs->fields['opbal']),
                    'ap_inv'        => floatval($rs->fields['ap_inv']),
                    'ap_pay'        => floatval($rs->fields['ap_pay']),
                    'closbal'       => floatval($rs->fields['closbal'])
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {

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
                    'nama_dokter'     => $row['nama_dokter'],
                    'opbal'         => floatval($row['opbal']),
                    'ap_inv'        => floatval($row['ap_inv']),
                    'ap_pay'        => floatval($row['ap_pay']),
                    'closbal'       => floatval($row['closbal']),
                );

                $sqli = "SELECT * FROM temp_summary_ap WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */
    } /*}}}*/
}
?>