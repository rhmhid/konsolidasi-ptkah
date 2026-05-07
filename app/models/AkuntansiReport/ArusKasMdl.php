<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class ArusKasMdl extends DB
{
    static $kode_kah, $kode_rsjk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        self::$kode_kah = dataConfigs('default_kode_branch_kah');

        self::$kode_rsjk = dataConfigs('default_kode_branch_rsjk');
    } /*}}}*/

    public static function list ($mytipe, $data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = $data['bid'];
        $month = $data['month'];
        $year = $data['year'];
        $status_cabang = $data['status_cabang'];
        $status_coa = $data['status_coa'];
        $record = [];
        $addsql = "";
        $optionsCabang = FilterCabang($bid);

        if ($mytipe == 'cf-direct-daily')
        {
            $sdate = $data['sdate'];
            $edate = $data['edate'];
        }
        else
        {
            if ($month > 12)
            {
                $sdate = date('Y-m-d', strtotime($year.'-12-01'));
                $edate = date("Y-m-t", strtotime($sdate));
            }
            else
            {
                $sdate = date('Y-m-d', strtotime($year.'-'.$month.'-01'));
                $edate = date("Y-m-t", strtotime($sdate));
            }
        }

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_cashflow");

        $sqli = "CREATE TEMPORARY TABLE temp_cashflow (
                    branch_code     VARCHAR,
                    coacode         VARCHAR,
                    coaname         VARCHAR,
                    debet           NUMERIC(18,2) DEFAULT 0,
                    amount          NUMERIC(18,2) DEFAULT 0
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, cc.coacode, cc.coaname
                        , bb.debet, (bb.credit - bb.debet) AS amount
                    FROM (
                        SELECT gl.glid
                        FROM general_ledger_d gld
                        INNER JOIN general_ledger gl ON gl.glid = gld.glid
                        INNER JOIN m_coa mc ON mc.coaid = gld.coaid
                        WHERE DATE(gl.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                            AND mc.coagid IN (".Modules::$cashflow_id.") AND gl.is_posted = 't'
                        GROUP BY gl.glid
                    ) aa
                    INNER JOIN general_ledger_d bb ON bb.glid = aa.glid
                    INNER JOIN m_coa cc ON cc.coaid = bb.coaid
                    INNER JOIN branch br ON bb.bid = br.bid
                    WHERE cc.coagid NOT IN (".Modules::$cashflow_id.")";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'debet'         => floatval($rs->fields['debet']),
                    'amount'        => floatval($rs->fields['amount'])
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT cc.coacode, cc.coaname
                        , bb.debet, (bb.credit - bb.debet) AS amount
                    FROM (
                        SELECT gl.glid
                        FROM general_ledger_d gld
                        INNER JOIN general_ledger gl ON gl.glid = gld.glid
                        INNER JOIN m_coa mc ON mc.coaid = gld.coaid
                        WHERE DATE(gl.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                            AND mc.coagid IN (".Modules::$cashflow_id.") AND gl.is_posted = 't'
                        GROUP BY gl.glid
                    ) aa
                    INNER JOIN general_ledger_d bb ON bb.glid = aa.glid
                    INNER JOIN m_coa cc ON cc.coaid = bb.coaid
                    WHERE cc.coagid NOT IN (".Modules::$cashflow_id.")";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'debet'         => floatval($rs->fields['debet']),
                    'amount'        => floatval($rs->fields['amount'])
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. KAH */

        /* B: Get Data RSJK */
        if ($optionsCabang['conn_rsjk'])
        {
            $rsjk_code = dataConfigs('default_kode_branch_rsjk');

            $endpoint = 'pass/cash_flow_direct';
            $payload = [
                'data' => [
                    'rmonth' => $month,
                    'ryear'  => $year
                ]
            ];

            $response = Bridging::post($rsjk_code, $endpoint, $payload);

            if ($response['status'] === 'success' && !empty($response['data']))
            {
                foreach ($response['data'] as $row)
                {
                    $record[] = array(
                        'branch_code'   => $rsjk_code,
                        'coacode'       => $row['coacode'], 
                        'coaname'       => $row['coaname'],
                        'debet'         => floatval($row['debet']),
                        'amount'        => floatval($row['amount']),
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
                    'coacode'       => $row['coacode'],
                    'coaname'       => $row['coaname'],
                    'debet'         => floatval($row['debet']),
                    'amount'        => floatval($row['amount'])
                );

                $sqli = "SELECT * FROM temp_cashflow WHERE 1 = 2";
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
        $sql = "SELECT aaa.branch_code, COALESCE(aaa.pcfid, 0) AS pcfid, aaa.parent_pcfid AS parent_pcfid
                    , aaa.pcfid_parent AS pcfid_parent, SUM(COALESCE(aaa.amount, 0)) AS amount
                FROM (
                    SELECT b.branch_code, tmp.debet, tmp.amount
                        , (CASE WHEN b.coacode = '115001' AND tmp.debet > 0 THEN 26
                        WHEN b.coacode = '115002' AND tmp.debet > 0 THEN 46
                        ELSE dd.pcfid END) AS pcfid -- hardcode
                        , dd.parent_pcfid, ee.parent_pcfid AS pcfid_parent
                    FROM temp_cashflow tmp
                    INNER JOIN (
                        SELECT br.bid, br.branch_code, br.kdbid, mc.coaid, mct.coatype, mc.coatid, mc.coacode, mc.coaname
                            , mc.default_debet, (mc.coacode || ' ' || mc.coaname) AS mycoa, mcb.coacode_from, mcb.coacode_to
                            , mc.pcfdid
                        FROM m_coa mc
                        INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                        LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                        LEFT JOIN branch br ON mcb.bid = br.bid
                        WHERE mc.allow_post = 't' $addsql
                    ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                    LEFT JOIN pos_cashflow dd ON (CASE WHEN b.coacode = '115001' AND tmp.debet > 0 THEN 26
                                                WHEN b.coacode = '115002' AND tmp.debet > 0 THEN 46
                                                ELSE b.pcfdid END) = dd.pcfid
                    LEFT JOIN pos_cashflow ee ON dd.parent_pcfid = ee.pcfid
                ) aaa
                GROUP BY aaa.branch_code, pcfid, parent_pcfid, pcfid_parent";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    public static function direct_saldo ($mytipe, $data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = $data['bid'];
        $month = $data['month'];
        $year = $data['year'];
        $status_cabang = $data['status_cabang'];
        $status_coa = $data['status_coa'];
        $record = [];
        $addsql = "";
        $optionsCabang = FilterCabang($bid);

        if ($mytipe == 'cf-direct-daily')
        {
            $sdate = $data['sdate'];
            $edate = $data['edate'];
        }
        else
        {
            if ($month > 12)
            {
                $sdate = date('Y-m-d', strtotime($year.'-12-01'));
                $edate = date("Y-m-t", strtotime($sdate));
            }
            else
            {
                $sdate = date('Y-m-d', strtotime($year.'-'.$month.'-01'));
                $edate = date("Y-m-t", strtotime($sdate));
            }
        }

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_cashflow_saldo");

        $sqli = "CREATE TEMPORARY TABLE temp_cashflow_saldo (
                    branch_code     VARCHAR,
                    coacode         VARCHAR,
                    coaname         VARCHAR,
                    eamount         NUMERIC(18,2) DEFAULT 0,
                    bamount         NUMERIC(18,2) DEFAULT 0
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, mc.coacode, mc.coaname, SUM(gld.debet - gld.credit) AS eamount
                        , SUM(CASE WHEN DATE(gl.gldate) < DATE('$sdate') THEN (gld.debet - gld.credit) ELSE 0 END) AS bamount
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN m_coa mc ON mc.coaid = gld.coaid
                    INNER JOIN branch br ON br.bid = gl.bid
                    WHERE gl.is_posted = 't' AND DATE(gl.gldate) <= DATE('$edate') AND mc.coagid IN (".Modules::$cashflow_id.")
                    GROUP BY br.branch_code, mc.coacode, mc.coaname";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'eamount'       => floatval($rs->fields['eamount']),
                    'bamount'       => floatval($rs->fields['bamount'])
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT mc.coacode, mc.coaname, SUM(gld.debet - gld.credit) AS eamount
                        , SUM(CASE WHEN DATE(gl.gldate) < DATE('$sdate') THEN (gld.debet - gld.credit) ELSE 0 END) AS bamount
                    FROM general_ledger_d gld
                    INNER JOIN general_ledger gl ON gl.glid = gld.glid
                    INNER JOIN m_coa mc ON mc.coaid = gld.coaid
                    WHERE gl.is_posted = 't' AND DATE(gl.gldate) <= DATE('$edate') AND mc.coagid IN (".Modules::$cashflow_id.")
                    GROUP BY mc.coacode, mc.coaname";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'eamount'       => floatval($rs->fields['eamount']),
                    'bamount'       => floatval($rs->fields['bamount'])
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
                    'coacode'       => $row['coacode'],
                    'coaname'       => $row['coaname'],
                    'eamount'       => floatval($row['eamount']),
                    'bamount'       => floatval($row['bamount'])
                );

                $sqli = "SELECT * FROM temp_cashflow_saldo WHERE 1 = 2";
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
        $sql = "SELECT b.*, tmp.eamount, tmp.bamount
                FROM temp_cashflow_saldo tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, br.kdbid, mc.coaid, mct.coatype, mc.coatid, mc.coacode, mc.coaname
                        , mc.default_debet, (mc.coacode || ' ' || mc.coaname) AS mycoa, mcb.coacode_from, mcb.coacode_to
                        , mc.pcfdid
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    WHERE mc.allow_post = 't' $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                ORDER BY b.coacode";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    public static function list_pos ($jenis_pos) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_cashflow WHERE is_aktif = 't' AND jenis_pos = ? ORDER BY urutan";
        $rs = DB::Execute($sql, [$jenis_pos]);

        return $rs;
    } /*}}}*/

    public static function direct_coa ($mytipe, $data, $pcfid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = $data['bid'];
        $month = $data['month'];
        $year = $data['year'];
        $status_cabang = $data['status_cabang'];
        $status_coa = $data['status_coa'];
        $record = [];
        $addsql = "";
        $optionsCabang = FilterCabang($bid);

        if ($mytipe == 'cf-direct-daily')
        {
            $sdate = $data['sdate'];
            $edate = $data['edate'];
        }
        else
        {
            if ($month > 12)
            {
                $sdate = date('Y-m-d', strtotime($year.'-12-01'));
                $edate = date("Y-m-t", strtotime($sdate));
            }
            else
            {
                $sdate = date('Y-m-d', strtotime($year.'-'.$month.'-01'));
                $edate = date("Y-m-t", strtotime($sdate));
            }
        }

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_cashflow_trans");

        $sqli = "CREATE TEMPORARY TABLE temp_cashflow_trans (
                    branch_code VARCHAR,
                    gldate      TIMESTAMP,
                    coacode     VARCHAR,
                    coaname     VARCHAR,
                    gldoc       VARCHAR,
                    gltype      VARCHAR,
                    gldesc      VARCHAR,
                    glnotes     VARCHAR,
                    debet       NUMERIC(18,2) DEFAULT 0,
                    credit      NUMERIC(18,2) DEFAULT 0,
                    gluser      VARCHAR
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT br.branch_code, bb.gldate, cc.coacode, cc.coaname
                        , format_glcode(bb.gldate, aa.doc_code, aa.gldoc, aa.glid) AS doc_no
                        , aa.journal_name, aa.gldesc, bb.notes, bb.debet, bb.credit, ps.nama_lengkap
                    FROM (
                        SELECT a.glid, a.gldesc, a.gldoc, d.doc_code, d.journal_name
                        FROM general_ledger_d b
                        INNER JOIN general_ledger a ON a.glid = b.glid
                        INNER JOIN m_coa c ON c.coaid = b.coaid
                        INNER JOIN journal_type d ON d.jtid = a.jtid
                        WHERE a.is_posted = 't' AND DATE(a.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                            AND c.coagid IN (".Modules::$cashflow_id.")
                        GROUP BY a.glid, a.gldesc, a.gldoc, d.doc_code, d.journal_name
                    ) aa
                    INNER JOIN general_ledger_d bb ON bb.glid = aa.glid
                    INNER JOIN m_coa cc ON cc.coaid = bb.coaid
                    INNER JOIN person ps ON ps.pid = bb.create_by
                    INNER JOIN branch br ON br.bid = bb.bid
                    WHERE cc.coagid NOT IN (".Modules::$cashflow_id.")";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'gldate'        => $rs->fields['gldate'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'gldoc'         => $rs->fields['doc_no'],
                    'gltype'        => $rs->fields['journal_name'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'glnotes'       => $rs->fields['notes'],
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'gluser'        => $rs->fields['nama_lengkap']
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT bb.gldate, cc.coacode, cc.coaname
                        , format_glcode(bb.gldate, aa.doc_code, aa.gldoc, aa.glid) AS doc_no
                        , aa.journal_name, aa.gldesc, bb.notes, bb.debet, bb.credit, ps.nama_lengkap
                    FROM (
                        SELECT a.glid, a.gldesc, a.gldoc, d.doc_code, d.journal_name
                        FROM general_ledger_d b
                        INNER JOIN general_ledger a ON a.glid = b.glid
                        INNER JOIN m_coa c ON c.coaid = b.coaid
                        INNER JOIN journal_type d ON d.jtid = a.jtid
                        WHERE a.is_posted = 't' AND DATE(a.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                            AND c.coagid IN (".Modules::$cashflow_id.")
                        GROUP BY a.glid, a.gldesc, a.gldoc, d.doc_code, d.journal_name
                    ) aa
                    INNER JOIN general_ledger_d bb ON bb.glid = aa.glid
                    INNER JOIN m_coa cc ON cc.coaid = bb.coaid
                    INNER JOIN person ps ON ps.pid = bb.create_by
                    WHERE cc.coagid NOT IN (".Modules::$cashflow_id.")";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'gldate'        => $rs->fields['gldate'],
                    'coacode'       => $rs->fields['coacode'],
                    'coaname'       => $rs->fields['coaname'],
                    'gldoc'         => $rs->fields['doc_no'],
                    'gltype'        => $rs->fields['journal_name'],
                    'gldesc'        => $rs->fields['gldesc'],
                    'glnotes'       => $rs->fields['notes'],
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'gluser'        => $rs->fields['nama_lengkap']
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
                    'gldate'        => $row['gldate'],
                    'coacode'       => $row['coacode'],
                    'coaname'       => $row['coaname'],
                    'gldoc'         => $row['gldoc'],
                    'gltype'        => $row['gltype'],
                    'gldesc'        => $row['gldesc'],
                    'glnotes'       => $row['glnotes'],
                    'debet'         => floatval($row['debet']),
                    'credit'        => floatval($row['credit']),
                    'gluser'        => $row['gluser']
                );

                $sqli = "SELECT * FROM temp_cashflow_trans WHERE 1 = 2";
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
        $sql = "SELECT b.*, tmp.gldate, tmp.gldoc, tmp.gltype, tmp.gldesc, tmp.glnotes, tmp.debet, tmp.credit
                    , tmp.gluser, (tmp.credit - tmp.debet) AS amount
                FROM temp_cashflow_trans tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, br.branch_name, br.is_primary, br.kdbid, mc.coaid
                        , mc.coacode, mc.coaname, mc.default_debet, (mc.coacode || ' ' || mc.coaname) AS mycoa
                        , mcb.coacode_from, mcb.coacode_to, mc.pcfdid
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    WHERE mc.allow_post = 't' $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                WHERE COALESCE((CASE WHEN b.coacode = '115001' AND tmp.debet > 0 THEN 26
                    WHEN b.coacode = '115002' AND tmp.debet > 0 THEN 46
                    ELSE b.pcfdid END), 0) = $pcfid
                ORDER BY b.is_primary, b.bid, tmp.gldate";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    public static function get_cf_name ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT nama_pos FROM pos_cashflow WHERE pcfid = ?";
        $rs = DB::GetOne($sql, [$myid]);

        return $rs;
    } /*}}}*/
}
?>