<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class NeracaMdl extends DB
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

        $sql = "SELECT c.coatid, UPPER(c.coatype) AS coatype, b.coaid, b.coacode, b.coaname, b.default_debet, COALESCE(b.pnid, 0) AS pnid
                    , (CASE WHEN b.coaid IN (".Modules::$laba_periode_lalu.", ".Modules::$laba_periode_berjalan.") THEN
                        0
                    ELSE
                        COALESCE((CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE {$opbal} END), 0)
                    END) AS openingbal
                    , (CASE WHEN a.paid != {$paid} THEN a.closingbal ELSE ({$opbal} + a.amount{$month}) END) AS closingbal
                FROM ledger_summary a, m_coa b, m_coatype c, periode_akunting d
                WHERE b.coaid = a.coaid AND c.coatid = b.coatid AND d.paid = a.paid
                    AND d.pend = (SELECT MAX(d.pend)
                        FROM ledger_summary e, periode_akunting d
                        WHERE d.paid = e.paid AND e.coaid = a.coaid AND d.pend <= DATE('{$pend}'))
                    AND b.coatid <= 3 AND a.bid = ?
                ORDER BY c.coatid, b.coacode";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/

    public static function list_pos () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM pos_neraca WHERE is_aktif = 't' ORDER BY urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>