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
                    pcfid           INT,
                    parent_pcfid    INT,
                    pcfid_parent    INT,
                    amount          NUMERIC(18,2) DEFAULT 0
                ) ON COMMIT PRESERVE ROWS;";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        /* B: Get Data PT. JKK */
        if ($optionsCabang['conn_jkk'])
        {
            $sql = "SELECT aaa.branch_code, COALESCE(aaa.pcfid, 0) AS pcfid, aaa.parent_pcfid, aaa.pcfid_parent
                        , SUM(COALESCE(aaa.amount, 0)) AS amount
                    FROM (
                        SELECT br.branch_code, aa.glid, (bb.credit - bb.debet) AS amount
                            , (CASE WHEN cc.coacode = '115001' AND bb.debet > 0 THEN 26
                            WHEN cc.coacode = '115002' AND bb.debet > 0 THEN 46
                            ELSE cc.pcfdid END) AS pcfid -- hardcode
                            , dd.parent_pcfid, ee.parent_pcfid AS pcfid_parent
                        FROM (
                            SELECT gl.glid
                            FROM general_ledger_d gld
                            JOIN general_ledger gl ON gl.glid = gld.glid
                            JOIN (SELECT coaid FROM m_coa WHERE coagid IN (".Modules::$cashflow_id.")) mc ON mc.coaid = gld.coaid
                            WHERE DATE(gl.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')
                                AND gl.is_posted = 't'
                            GROUP BY gl.glid
                        ) aa
                        JOIN general_ledger_d bb ON bb.glid = aa.glid
                        JOIN m_coa cc ON cc.coaid = bb.coaid
                        LEFT JOIN pos_cashflow dd ON (CASE WHEN cc.coacode = '115001' AND bb.debet > 0 THEN 26
                                                    WHEN cc.coacode = '115002' AND bb.debet > 0 THEN 46
                                                    ELSE cc.pcfdid END) = dd.pcfid
                        LEFT JOIN pos_cashflow ee ON dd.parent_pcfid = ee.pcfid
                        LEFT JOIN branch br ON bb.bid = br.bid
                        WHERE bb.coaid NOT IN (SELECT coaid FROM m_coa WHERE coagid IN (".Modules::$cashflow_id."))
                    ) aaa
                    GROUP BY aaa.branch_code, aaa.pcfid, aaa.parent_pcfid, aaa.pcfid_parent";
            $rs = DB2::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'pcfid'         => $rs->fields['pcfid'],
                    'parent_pcfid'  => $rs->fields['parent_pcfid'],
                    'pcfid_parent'  => $rs->fields['pcfid_parent'],
                    'amount'        => floatval($rs->fields['amount'])
                );

                $rs->MoveNext();
            }
        }
        /* E: Get Data PT. JKK */

        /* B: Get Data PT. KAH */
        if ($optionsCabang['conn_kah'])
        {
            $sql = "SELECT COALESCE(aaa.pcid, 0) AS pcfid, aaa.parent_pcid AS parent_pcfid, aaa.pcid_parent AS pcfid_parent
                        , SUM(COALESCE(aaa.amount, 0)) AS amount
                    FROM (
                        SELECT aa.glid, (bb.credit - bb.debet) AS amount
                            , (CASE WHEN cc.coacode = '115001' AND bb.debet > 0 THEN 26
                            WHEN cc.coacode = '115002' AND bb.debet > 0 THEN 46
                            ELSE dd.pcid END) AS pcid -- hardcode
                            , dd.parent_pcid, ee.parent_pcid AS pcid_parent
                        FROM (
                            SELECT gl.glid
                            FROM general_ledger_d gld
                            JOIN general_ledger gl ON gl.glid = gld.glid
                            JOIN (SELECT coaid FROM m_coa WHERE coagid IN (".Modules::$cashflow_id.")) mc ON mc.coaid = gld.coaid
                            WHERE DATE(gl.gldate) BETWEEN DATE('$sdate') AND DATE('$edate') AND gl.is_posted = 't'
                            GROUP BY gl.glid
                        ) aa
                        JOIN general_ledger_d bb ON bb.glid = aa.glid
                        JOIN m_coa cc ON cc.coaid = bb.coaid
                        LEFT JOIN pos_cashflow dd ON (CASE WHEN cc.coacode = '115001' AND bb.debet > 0 THEN 26
                                                    WHEN cc.coacode = '115002' AND bb.debet > 0 THEN 46
                                                    ELSE cc.pcdid END) = dd.pcid
                        LEFT JOIN pos_cashflow ee ON dd.parent_pcid = ee.pcid
                        WHERE bb.coaid NOT IN (SELECT coaid FROM m_coa WHERE coagid IN (".Modules::$cashflow_id."))
                    ) aaa
                    GROUP BY pcfid, parent_pcfid, pcfid_parent";
            $rs = DB3::Execute($sql);

            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => self::$kode_kah,
                    'pcfid'         => $rs->fields['pcfid'],
                    'parent_pcfid'  => $rs->fields['parent_pcfid'],
                    'pcfid_parent'  => $rs->fields['pcfid_parent'],
                    'amount'        => floatval($rs->fields['amount'])
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
                    'pcfid'         => $row['pcfid'],
                    'parent_pcfid'  => $row['parent_pcfid'],
                    'pcfid_parent'  => $row['pcfid_parent'],
                    'amount'        => floatval($row['amount_period'])
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
        $sql = "SELECT b.*, tmp.branch_code, tmp.pcfid, tmp.parent_pcfid, tmp.pcfid_parent, tmp.amount
                FROM temp_cashflow tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, br.kdbid, mc.coaid, mct.coatype, mc.coatid, mc.coacode, mc.coaname
                        , mc.default_debet, (mc.coacode || ' ' || mc.coaname) AS mycoa, mcb.coacode_from, mcb.coacode_to
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    -- LEFT JOIN pos_pl ppl ON mc.pplid = ppl.pplid
                    WHERE mc.allow_post = 't' $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/

    public static function direct_saldo ($mytipe, $data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $month = $data['month'];
        $year = $data['year'];

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

        $sql = "SELECT mc.coaid, mc.coacode, mc.coaname, SUM(gld.debet - gld.credit) AS eamount
                    , SUM(CASE WHEN DATE(gl.gldate) < DATE('$sdate') THEN (gld.debet - gld.credit) ELSE 0 END) AS bamount
                FROM general_ledger_d gld
                JOIN general_ledger gl ON gl.glid = gld.glid
                JOIN (
                    SELECT coaid, coaname, coacode
                    FROM m_coa
                    WHERE coagid IN (".Modules::$cashflow_id.")
                ) mc ON mc.coaid = gld.coaid
                WHERE gl.is_posted = 't' AND DATE(gl.gldate) <= DATE('$edate') AND gl.bid = $bid
                GROUP BY mc.coaid, mc.coacode, mc.coaname
                ORDER BY mc.coacode";
        $rs = DB::Execute($sql);

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

        $bid = Auth::user()->branch->bid;

        $month = $data['month'];
        $year = $data['year'];

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

        $addsql .= " AND COALESCE((CASE WHEN zzz.coacode = '115001' AND aaa.debet > 0 THEN 26
                        WHEN zzz.coacode = '115002' AND aaa.debet > 0 THEN 46
                        ELSE zzz.pcfdid END), 0) = ".$pcfid;

        $sql = "SELECT aaa.gldid, aaa.gldate, zzz.coacode, zzz.coaname, aaa.journal_name
                    , format_glcode(aaa.gldate, aaa.doc_code, aaa.gldoc, aaa.glid) AS doc_no
                    , aaa.gldesc, aaa.notes, ps.nama_lengkap AS user_input
                    , SUM(COALESCE(aaa.credit, 0) - COALESCE(aaa.debet, 0)) AS amount
                FROM (
                    SELECT aa.gldesc, aa.gldoc, aa.doc_code, aa.journal_name, bb.*
                    FROM (
                        SELECT a.glid, a.gldesc, a.gldoc, d.doc_code, d.journal_name
                        FROM general_ledger_d b
                        JOIN general_ledger a ON a.glid = b.glid
                        JOIN (SELECT coaid FROM m_coa WHERE coagid IN (".Modules::$cashflow_id.")) c ON c.coaid = b.coaid
                        JOIN journal_type d ON d.jtid = a.jtid
                        WHERE a.is_posted = 't' AND DATE(a.gldate) BETWEEN DATE('$sdate') AND DATE('$edate') AND a.bid = $bid
                        GROUP BY a.glid, a.gldesc, a.gldoc, d.doc_code, d.journal_name
                    ) aa
                    JOIN general_ledger_d bb ON bb.glid = aa.glid AND bb.bid = $bid
                    WHERE bb.coaid NOT IN (SELECT coaid FROM m_coa WHERE coagid IN (".Modules::$cashflow_id."))
                ) aaa
                LEFT JOIN m_coa zzz ON aaa.coaid = zzz.coaid
                JOIN person ps ON ps.pid = aaa.create_by
                WHERE 1 = 1 $addsql
                GROUP BY aaa.gldid, aaa.gldate, zzz.coacode, zzz.coaname, aaa.journal_name
                    , doc_no, aaa.gldesc, aaa.notes, aaa.create_by, user_input
                ORDER BY aaa.gldate";
        $rs = DB::Execute($sql);

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