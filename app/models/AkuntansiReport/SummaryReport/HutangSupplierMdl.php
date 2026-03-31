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
        $jurnal_speriod = $data['jurnal_speriod'];
        $jurnal_eperiod = $data['jurnal_eperiod'];
        $bid  = $data['bid'];
        $is_posted = $data['is_posted'];
        $gldoc = strtolower(trim($data['gldoc']));
        $keterangan = strtolower(trim($data['keterangan']));
        $record = [];
        $optionsCabang = FilterCabang($bid);

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_general_ledger");

        $sqli = "CREATE TEMPORARY TABLE temp_general_ledger (
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

        DB::Execute("CREATE INDEX idx_temp_glid ON temp_general_ledger(glid);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            if ($is_posted) $addsql .= " AND a.is_posted = '$is_posted'";

            if ($gldoc) $addsql .= " AND format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) = '$gldoc'";

            if ($keterangan) $addsql .= " AND LOWER(a.keterangan) LIKE '%$keterangan%'";

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
            if ($is_posted) $addsql .= " AND a.is_posted = '$is_posted'";

            if ($gldoc) $addsql .= " AND format_glcode(a.gldate, b.doc_code, a.gldoc, a.glid) = '$gldoc'";

            if ($keterangan) $addsql .= " AND LOWER(a.keterangan) LIKE '%$keterangan%'";

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

                $sqli = "SELECT * FROM temp_general_ledger WHERE 1 = 2";
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
                FROM temp_general_ledger tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql2
                ORDER BY tmp.gldate DESC, tmp.glid DESC";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    public static function detail_jurnal ($myglid, $mybid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $addsql = "";
        $optionsCabang = FilterCabang($bid);
        $record = [];
        $bcode = DB::GetOne("SELECT branch_code FROM branch WHERE bid = ?", [$mybid]);

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_detail_jurnal");

        $sqli = "CREATE TEMPORARY TABLE temp_detail_jurnal (
                    branch_code VARCHAR,
                    gldid BIGINT,
                    glid BIGINT,
                    gldesc TEXT,
                    gldate TIMESTAMP,
                    journal_name VARCHAR(255),
                    create_time TIMESTAMP,
                    gldoc VARCHAR(255),
                    posted VARCHAR(50),
                    coacode VARCHAR(50),
                    coaname VARCHAR(255),
                    debet NUMERIC(18, 2),
                    credit NUMERIC(18, 2),
                    notes TEXT,
                    posted_by VARCHAR(255),
                    supp_cust VARCHAR(255),
                    reff_code VARCHAR(100),
                    cost_center VARCHAR(255)
                );";
        DB::Execute($sqli);

        DB::Execute("CREATE INDEX idx_temp_detail_gldid ON temp_detail_jurnal(gldid);");
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, a.gldid, b.glid, b.gldesc, b.gldate, b.is_posted, d.journal_name, b.create_time
                        , format_glcode(b.gldate, d.doc_code, b.gldoc, b.glid) AS gldoc
                        , (CASE WHEN b.is_posted THEN 'POSTED' ELSE 'NOT POSTED' END) AS posted
                        , b.jtid, c.coacode, c.coaname, a.debet, a.credit, a.notes, e.nama_lengkap AS posted_by
                        , (CASE WHEN b.jtid IN (4, 9, 20, 21, 22, 23, 24, 25, 26, 29) 
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
                    INNER JOIN branch br ON br.bid = b.bid
                    WHERE b.glid = ? AND br.branch_code = ?";
            $rs = DB2::Execute($sql, [$myglid, $bcode]);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'gldid'         => $rs->fields['gldid'],
                    'glid'          => $rs->fields['glid'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'gldate'        => $rs->fields['gldate'],
                    'posted'        => $rs->fields['posted'],
                    'journal_name'  => $rs->fields['journal_name'],
                    'create_time'   => $rs->fields['create_time'],
                    'gldoc'         => $rs->fields['gldoc'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'notes'         => $rs->fields['notes'],
                    'posted_by'     => $rs->fields['posted_by'],
                    'supp_cust'     => $rs->fields['supp_cust'],
                    'reff_code'     => $rs->fields['reff_code'],
                    'cost_center'   => $rs->fields['cost_center'],
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT a.gldid, b.glid, b.gldesc, b.gldate, b.is_posted, d.journal_name, b.create_time
                        ,format_glcode(b.gldate, d.doc_code, b.gldoc, b.glid) AS gldoc, b.jtid
                        , (CASE WHEN b.is_posted THEN 'POSTED' ELSE 'NOT POSTED' END) AS posted, b.reff_code
                        , c.coacode, c.coaname, a.debet, a.credit, a.notes, e.nama_lengkap AS posted_by
                        , (CASE WHEN b.jtid IN (4, 9, 20, 21, 22, 23, 24) THEN f.nama_supp ELSE g.nama_customer END) AS supp_cust
                    FROM general_ledger_d a
                    INNER JOIN general_ledger b ON b.glid = a.glid
                    INNER JOIN m_coa c ON c.coaid = a.coaid
                    INNER JOIN journal_type d ON d.jtid = b.jtid
                    INNER JOIN person e ON e.pid = b.create_by
                    LEFT JOIN m_supplier f ON b.suppid = f.suppid
                    LEFT JOIN m_customer g ON b.suppid = g.custid
                    WHERE b.glid = ?
                    ORDER BY a.gldid";
            $rs = DB3::Execute($sql, [$myglid]);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'gldid'         => $rs->fields['gldid'],
                    'glid'          => $rs->fields['glid'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'gldate'        => $rs->fields['gldate'],
                    'posted'        => $rs->fields['posted'],
                    'journal_name'  => $rs->fields['journal_name'],
                    'create_time'   => $rs->fields['create_time'],
                    'gldoc'         => $rs->fields['gldoc'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'notes'         => $rs->fields['notes'],
                    'posted_by'     => $rs->fields['posted_by'],
                    'supp_cust'     => $rs->fields['supp_cust'],
                    'reff_code'     => $rs->fields['reff_code'],
                    'cost_center'   => $rs->fields['cost_center'],
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
                    'gldid'         => $row['gldid'],
                    'glid'          => $row['glid'],
                    'gldesc'        => $row['gldesc'],
                    'gldate'        => $row['gldate'],
                    'posted'        => $row['posted'],
                    'journal_name'  => $row['journal_name'],
                    'create_time'   => $row['create_time'],
                    'gldoc'         => $row['gldoc'],
                    'coacode'       => $row['coacode'],
                    'coaname'       => $row['coaname'],
                    'debet'         => floatval($row['debet']),
                    'credit'        => floatval($row['credit']),
                    'notes'         => $row['notes'],
                    'posted_by'     => $row['posted_by'],
                    'supp_cust'     => $row['supp_cust'],
                    'reff_code'     => $row['reff_code'],
                    'cost_center'   => $row['cost_center'],
                );

                $sqli = "SELECT * FROM temp_detail_jurnal WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }
        /* E: Insert To Temp Table */

        $addsql .= $optionsCabang['query'];

        /* B: Showing Data From Temp Table */
        $sql = "SELECT tmp.*, br.bid, br.branch_name
                FROM temp_detail_jurnal tmp
                INNER JOIN branch br ON br.branch_code = tmp.branch_code
                WHERE 1 = 1 $addsql
                ORDER BY tmp.gldid";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/
}
?>