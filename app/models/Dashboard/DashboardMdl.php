<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class DashboardMdl extends DB
{
    static $kode_kah, $kode_rsjk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        self::$kode_kah = dataConfigs('default_kode_branch_kah');

        self::$kode_rsjk = dataConfigs('default_kode_branch_rsjk');
    } /*}}}*/

    public static function list ($now, $before, $tahun) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $optionsCabang = FilterCabang($bid);

        $opbal = 'a.openingbal';
        for ($i = 1; $i <= $emonth; $i++)
            $opbal .= ' + a.amount'.$i;

        /* B: Create Temp Table */
        DB::Execute("DROP TABLE IF EXISTS temp_profit_loss_gabungan");

        $sqli = "CREATE TEMPORARY TABLE temp_profit_loss_gabungan (
                    branch_code     VARCHAR,
                    coaid           INT,
                    coatype         VARCHAR,
                    coatid          INT,
                    coacode         VARCHAR,
                    coaname         VARCHAR,
                    default_debet   BOOLEAN,
                    amount_bln_prev NUMERIC(18,2) DEFAULT 0,
                    amount_bln      NUMERIC(18,2) DEFAULT 0,
                    closingbal      NUMERIC(18,2) DEFAULT 0,
                    pplid           INT,
                    tahun           INT,
                    pplrid          INT
                ) ON COMMIT PRESERVE ROWS;";
        DB::Execute($sqli);
        /* E: Create Temp Table */

        foreach ($now as $row_now)
        {
            $record = array(
                'branch_code'     => $row['branch_code'],
                'coaid'           => $row['coaid'],
                'coatype'         => $row['coatype'],
                'coatid'          => $row['coatid'],
                'coacode'         => $row['coacode'],
                'coaname'         => $row['coaname'],
                'default_debet'   => $row['default_debet'],
                'amount_bln_prev' => floatval($row['amount_bln_prev']),
                'amount_bln'      => floatval($row['amount_bln']),
                'closingbal'      => floatval($row['closingbal']),
                'pplid'           => $row['pplid'],
                'pplrid'          => $row['pplrid'],
                'tahun'           => $tahun
            );

            foreach ($row_now as $key => $value)
                $record[$key] = $value;

            $final_result[] = $record;
        }

        foreach ($before as $row_before)
        {
            $record = array(
                'branch_code'     => $row['branch_code'],
                'coaid'           => $row['coaid'],
                'coatype'         => $row['coatype'],
                'coatid'          => $row['coatid'],
                'coacode'         => $row['coacode'],
                'coaname'         => $row['coaname'],
                'default_debet'   => $row['default_debet'],
                'amount_bln_prev' => floatval($row['amount_bln_prev']),
                'amount_bln'      => floatval($row['amount_bln']),
                'closingbal'      => floatval($row['closingbal']),
                'pplid'           => $row['pplid'],
                'pplrid'          => $row['pplrid'],
                'tahun'           => $tahun -1
            );

            foreach ($row_before as $key => $value)
                $record[$key] = $value;

            $final_result[] = $record;
        }

        $ok = true;
        if (!empty($final_result))
        {
            foreach ($final_result as $idx => $row)
            {
                $data = array(
                    'branch_code'       => $row['branch_code'],
                    'coaid'             => $row['coaid'],
                    'coatype'           => $row['coatype'],
                    'coatid'            => $row['coatid'],
                    'coacode'           => $row['coacode'],
                    'coaname'           => $row['coaname'],
                    'default_debet'     => $row['default_debet'],
                    'amount_bln_prev'   => floatval($row['amount_bln_prev']),
                    'amount_bln'        => floatval($row['amount_bln']),
                    'closingbal'        => floatval($row['closingbal']),
                    'pplid'             => $row['pplid'],
                    'pplrid'            => $row['pplrid'],
                    'tahun'             => $row['tahun'],
                );

                $sqli = "SELECT * FROM temp_profit_loss_gabungan WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }

        $addsql .= $optionsCabang['query'];

        $tahunbefore = $tahun - 1;

        /* B: Showing Data From Temp Table */
        $sql = "SELECT b.*, tmp.branch_code
                    , (CASE WHEN tmp.tahun = $tahun THEN tmp.amount_bln ELSE 0 END) AS amount_bln
                    , (CASE WHEN tmp.tahun = $tahunbefore THEN tmp.amount_bln ELSE 0 END) AS amount_bln_before
                    , (CASE WHEN tmp.tahun = $tahun THEN tmp.amount_bln_prev ELSE 0 END) AS amount_bln_prev
                    , (CASE WHEN tmp.tahun = $tahunbefore THEN tmp.amount_bln_prev ELSE 0 END) AS amount_bln_prev_before
                    , (CASE WHEN tmp.tahun = $tahun THEN tmp.closingbal ELSE 0 END) AS closingbal
                    , (CASE WHEN tmp.tahun = $tahunbefore THEN tmp.closingbal ELSE 0 END) AS closingbal_before
                FROM temp_profit_loss_gabungan tmp
                INNER JOIN (
                    SELECT br.bid, br.branch_code, br.kdbid, mc.coaid, mct.coatype, mc.coatid, mc.coacode, mc.coaname
                        , mc.default_debet, (mc.coacode || ' ' || mc.coaname) AS mycoa, mcb.coacode_from, mcb.coacode_to
                        , COALESCE(mc.pplid, 0) AS pplid, COALESCE(ppl.pplrid, 0) AS pplrid
                    FROM m_coa mc
                    INNER JOIN m_coatype mct ON mct.coatid = mc.coatid
                    LEFT JOIN m_coa_branch mcb ON mc.coaid = mcb.coaid
                    LEFT JOIN branch br ON mcb.bid = br.bid
                    LEFT JOIN pos_pl ppl ON mc.pplid = ppl.pplid
                    WHERE mc.allow_post = 't' AND mc.coatid > 3 $addsql
                ) b ON b.branch_code = tmp.branch_code AND tmp.coacode BETWEEN b.coacode_from AND b.coacode_to
                ORDER BY (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END), b.coacode";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/
}
?>