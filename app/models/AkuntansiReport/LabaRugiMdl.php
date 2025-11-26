<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class LabaRugiMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $smonth = $data['prev_month'];
        $syear = $data['prev_year'];
        $emonth = $data['month'];
        $eyear = $data['year'];
        $paid = $data['paid'];
        $pbegin = $data['pbegin'];
        $pend = $data['pend'];

        $opbal = 'a.openingbal';
        for ($i = 1; $i <= $emonth; $i++)
            $opbal .= ' + a.amount'.$i;

        $sql = "SELECT c.coatype, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END) AS coatid
                    , b.coacode, b.coaname, b.default_debet, (b.coacode || ' ' || b.coaname) AS mycoa, COALESCE(b.pplid, 0) AS pplid
                    , COALESCE(a.amount{$smonth}, 0) AS amount_bln_prev, COALESCE(a.amount{$emonth}, 0) AS amount_bln
                    , ({$opbal}) AS closingbal, COALESCE(e.pplrid, 0) AS pplrid
                FROM ledger_summary a
                JOIN m_coa b ON b.coaid = a.coaid
                JOIN m_coatype c ON c.coatid = b.coatid
                JOIN periode_akunting d ON d.paid = a.paid
                LEFT JOIN pos_pl e ON b.pplid = e.pplid
                WHERE b.coatid > 3 AND a.paid = ? AND a.bid = ?
                ORDER BY (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END), b.coacode";
        $rs = DB::Execute($sql, [$paid, $bid]);

        return $rs;
    } /*}}}*/

    public static function list_daily ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $pmonth = $data['pmonth'];

        $sql = "SELECT c.coatype, gld.coaid, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END) AS coatid
                    , b.coacode, b.coaname, b.default_debet, (b.coacode || ' ' || b.coaname) AS mycoa, COALESCE(b.pplid, 0) AS pplid
                    , SUM(gld.credit - gld.debet) AS amount_untill
                    , SUM(CASE WHEN DATE(gl.gldate) BETWEEN '$sdate' AND '$edate' THEN gld.credit - gld.debet ELSE 0 END) AS amount_period
                    , COALESCE(e.pplrid, 0) AS pplrid
                FROM general_ledger gl
                JOIN general_ledger_d gld ON gl.glid = gld.glid
                JOIN m_coa b ON b.coaid = gld.coaid
                JOIN m_coatype c ON c.coatid = b.coatid
                LEFT JOIN pos_pl e ON b.pplid = e.pplid
                WHERE b.coatid > 3 AND DATE(gl.gldate) BETWEEN DATE('$pmonth') AND DATE('$edate') AND gl.bid = ?
                GROUP BY c.coatype, gld.coaid, (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END)
                    , b.coacode, b.coaname, b.default_debet, mycoa, COALESCE(b.pplid, 0), COALESCE(e.pplrid, 0)
                ORDER BY (CASE WHEN b.coatid = 5 AND b.default_debet = 'f' THEN 4 ELSE b.coatid END), b.coacode";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/

    public static function list_pos_rekap () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_pl_rekap WHERE is_aktif = 't' ORDER BY urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_pl WHERE is_aktif = 't' ORDER BY urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>