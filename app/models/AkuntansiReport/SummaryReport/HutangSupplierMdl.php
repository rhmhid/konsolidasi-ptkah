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
        // $status_cabang = $data['status_cabang'];
        // $jurnal_speriod = $data['jurnal_speriod'];
        // $jurnal_eperiod = $data['jurnal_eperiod'];
        // $bid  = $data['bid'];
        // $is_posted = $data['is_posted'];
        // $gldoc = strtolower(trim($data['gldoc']));
        // $keterangan = strtolower(trim($data['keterangan']));
        $record = [];
        $optionsCabang = FilterCabang($bid);

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_summary_ap");

        $sqli = "CREATE TEMPORARY TABLE temp_summary_ap (
                    branch_code VARCHAR(255),
                    glid BIGINT,
                    gldate TIMESTAMP,
                    gldesc TEXT,
                    useri VARCHAR(255),
                    is_posted BOOLEAN,
                    gldoc VARCHAR(255),
                    journal_name VARCHAR(255)
                );";
        DB::Execute($sqli);

        DB::Execute("CREATE INDEX idx_temp_glid ON temp_summary_ap(glid);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT d.branch_code, a.glid, a.gldate, a.gldesc, c.nama_lengkap AS useri
                        , format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) AS gldoc
                        , b.journal_name, a.is_posted
                    FROM general_ledger a
                    INNER JOIN journal_type b ON b.jtid = a.jtid
                    INNER JOIN person c ON c.pid = a.create_by
                    INNER JOIN branch d ON d.bid = a.bid
                    WHERE DATE(a.gldate) BETWEEN DATE('$jurnal_speriod') AND DATE('$jurnal_eperiod')
                        $addsql";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'glid'          => $rs->fields['glid'],
                    'gldate'        => $rs->fields['gldate'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'useri'         => $rs->fields['useri'],
                    'gldoc'         => $rs->fields['gldoc'],
                    'journal_name'  => $rs->fields['journal_name'],
                    'is_posted'     => $rs->fields['is_posted']
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT a.glid, a.gldate, a.gldesc, c.nama_lengkap AS useri
                        , format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) AS gldoc
                        , b.journal_name, a.is_posted
                    FROM general_ledger a
                    INNER JOIN journal_type b ON b.jtid = a.jtid
                    INNER JOIN person c ON c.pid = a.create_by
                    WHERE DATE(a.gldate) BETWEEN DATE('$jurnal_speriod') AND DATE('$jurnal_eperiod')
                        $addsql";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'glid'          => $rs->fields['glid'],
                    'gldate'        => $rs->fields['gldate'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'useri'         => $rs->fields['useri'],
                    'gldoc'         => $rs->fields['gldoc'],
                    'journal_name'  => $rs->fields['journal_name'],
                    'is_posted'     => $rs->fields['is_posted']
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
                $data = array(
                    'branch_code'   => $row['branch_code'],
                    'glid'          => $row['glid'],
                    'gldate'        => $row['gldate'],
                    'gldesc'        => $row['gldesc'],
                    'useri'         => $row['useri'],
                    'gldoc'         => $row['gldoc'],
                    'journal_name'  => $row['journal_name'],
                    'is_posted'     => $row['is_posted']
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
                ORDER BY tmp.gldate DESC, tmp.glid DESC";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/
}
?>