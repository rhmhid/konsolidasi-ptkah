<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class AgingHutangUnbillMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        //         if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sdate = date('Y-m-d', strtotime($data['sdate']));
        $bid = $data['bid'];

        $optionsCabang = FilterCabang($bid);

//        print_R($optionsCabang);

        DB::Execute("DROP TABLE IF EXISTS temp_aging_hutang_unbill");

        $sqli = "CREATE TEMPORARY TABLE temp_aging_hutang_unbill (
                    branch_code   VARCHAR,
                    bid           INT,
                    grid          INT,
                    suppid        INT,
                    grdate        TIMESTAMP,
                    grcode        VARCHAR,
                    nama_supp     VARCHAR,
                    no_faktur     VARCHAR,
                    nominal       NUMERIC(18,2) DEFAULT 0
                ) ON COMMIT PRESERVE ROWS";
        DB::Execute($sqli);

        if ($optionsCabang['conn_jkk'])
        {

            $addsql = ($bid) ? ' AND a.bid = '.$bid : '';

            $sql = "SELECT a.grid, a.grdate, a.grcode, b.nama_supp, a.no_faktur
                        , (a.totalall - COALESCE(c.nominal_inv, 0)) AS nominal,
                        d.branch_code,d.bid
                    FROM good_receipt a
                    INNER JOIN m_supplier b ON b.suppid = a.suppid
                    LEFT JOIN (
                        SELECT aa.grid, SUM(aa.nominal) AS nominal_inv
                        FROM ap_supplier_d aa, ap_supplier bb
                        WHERE bb.apsid = aa.apsid AND DATE(bb.apdate) <= ?
                        GROUP BY aa.grid
                    ) c ON a.grid = c.grid
                    INNER JOIN branch d ON d.bid = a.bid
                    WHERE a.cara_beli = 2 AND DATE(a.grdate) <= ? 
                        AND (a.totalall - COALESCE(c.nominal_inv, 0)) <> 0
                        $addsql
                    ORDER BY a.grdate";
            $rs = DB2::Execute($sql, [$sdate, $sdate]);


            while (!$rs->EOF)
            {
                $record[] = array(
                    'branch_code'   => $rs->fields['branch_code'],
                    'bid'           => $rs->fields['bid'],
                    'grid'          => $rs->fields['grid'],
                    'suppid'        => $rs->fields['suppid'],
                    'grdate'        => $rs->fields['grdate'],
                    'grcode'        => $rs->fields['grcode'],
                    'nama_supp'     => $rs->fields['nama_supp'],
                    'no_faktur'     => $rs->fields['no_faktur'],
                    'nominal'       => floatval($rs->fields['nominal']),
                );

                $rs->MoveNext();
            }
        }

        $ok = true;
        if (!empty($record))
        {
            foreach ($record as $idx => $row)
            {
                $data = array(
                    'branch_code'     => $row['branch_code'],
                    'bid'             => $row['bid'],
                    'grid'            => $row['grid'],
                    'suppid'          => $row['suppid'],
                    'grdate'          => $row['grdate'],
                    'grcode'          => $row['grcode'],
                    'nama_supp'       => $row['nama_supp'],
                    'no_faktur'       => $row['no_faktur'],
                    'nominal'         => floatval($row['nominal']),
                );

                $sqli = "SELECT * FROM temp_aging_hutang_unbill WHERE 1 = 2";
                $rsi = DB::Execute($sqli);
                $sqli = DB::InsertSQL($rsi, $data);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }


        if ($status_cabang) $addsql .= " AND br.is_aktif = '$status_cabang'";

        if ($status_coa) $addsql .= " AND mc.is_valid = '$status_coa'";

        $addsql .= $optionsCabang['query'];

        /* B: Showing Data From Temp Table */
        $sql = "SELECT a.*,br.branch_name 
                FROM temp_aging_hutang_unbill a 
                LEFT JOIN branch br ON a.bid = br.bid
                WHERE 1=1 $addsql";
        $rs = DB::Execute($sql);
        /* E: Showing Data From Temp Table */

        return $rs;
    } /*}}}*/
}
?>