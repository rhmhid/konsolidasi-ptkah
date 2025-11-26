<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class NeracaSaldoMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $month = $data['month'];
        $year = $data['year'];
        $paid = $data['paid'];
        $pbegin = $data['pbegin'];
        $pend = $data['pend'];

        $opbal = 'a.openingbal';
        for ($i = 1; $i < $month; $i++)
            $opbal .= ' + a.amount'.$i;

        $sql = "SELECT a.coaid, c.coatype, b.coatid, b.coacode, b.coaname, b.default_debet, (b.coacode || ' ' || b.coaname) AS mycoa,
                    COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0) AS openingbal,
                    COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_debet END), 0) AS debet,
                    COALESCE((CASE WHEN a.paid != {$paid} THEN 0 ELSE a.amount{$month}_credit END), 0) AS credit
                FROM ledger_summary a, m_coa b, m_coatype c, periode_akunting d
                WHERE b.coaid = a.coaid AND c.coatid = b.coatid AND d.paid = a.paid
                    AND d.pend = (SELECT MAX(d.pend)
                        FROM ledger_summary e, periode_akunting d
                        WHERE d.paid = e.paid AND e.coaid = a.coaid AND d.pend <= DATE('{$pend}'))
                    AND a.bid = ?
                ORDER BY b.coacode";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/
}
?>