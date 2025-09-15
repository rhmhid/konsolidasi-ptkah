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
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sdate = date('Y-m-d', strtotime($data['sdate']));
        $suppid = $data['suppid'];

        if ($suppid) $addsql .= " AND a.suppid = ".$suppid;

        $sql = "SELECT a.grid, a.grdate, a.grcode, b.nama_supp, a.no_faktur
                    , (a.totalall - COALESCE(c.nominal_inv, 0)) AS nominal
                FROM good_receipt a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN (
                    SELECT aa.grid, SUM(aa.nominal) AS nominal_inv
                    FROM ap_supplier_d aa, ap_supplier bb
                    WHERE bb.apsid = aa.apsid AND DATE(bb.apdate) <= ?
                    GROUP BY aa.grid
                ) c ON a.grid = c.grid
                WHERE a.cara_beli = 2 AND DATE(a.grdate) <= ? AND a.bid = ?
                    AND (a.totalall - COALESCE(c.nominal_inv, 0)) <> 0
                    $addsql
                ORDER BY a.grdate";
        $rs = DB::Execute($sql, [$sdate, $sdate, $bid]);

        return $rs;
    } /*}}}*/
}
?>