<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class AgingHutangMdl extends DB
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
        $date_by = $data['date_by'];
        $suppid = $data['suppid'];
        $doctor_id = $data['doctor_id'];

        if ($suppid) $addsql .= " AND aa.suppid = ".$suppid;

        if ($doctor_id) $addsql .= " AND aa.doctor_id = ".$doctor_id;

        $sql = "SELECT aa.jtid, c.journal_name, aa.suppid, b.nama_supp, aa.no_inv
                    , aa.apdate, aa.duedate, aa.nominal, aa.up, aa.bid, pd.nama_lengkap AS nama_dokter
                FROM (
                    SELECT 20 AS jtid, a.suppid, a.no_invoice AS no_inv, a.apdate, a.duedate
                        , (a.amount/* - COALESCE(b.amount_pay, 0)*/) AS nominal
                        , (DATE('$sdate') - DATE(a.duedate)) AS up, NULL AS doctor_id
                        , a.bid
                    FROM ap_supplier a
                    -- LEFT JOIN (
                    --     SELECT x.apsid, SUM(x.nominal) AS amount_pay
                    --     FROM ap_payment_d x
                    --     INNER JOIN ap_payment y ON y.appid = x.appid
                    --     WHERE DATE(y.paydate) <= DATE('$sdate') AND y.bid = $bid
                    --     GROUP BY x.apsid
                    -- ) b ON a.apsid = b.apsid
                    WHERE DATE(a.{$date_by}) <= DATE('$sdate') AND (a.amount /*- COALESCE(b.amount_pay, 0)*/) <> 0
                        AND a.bid = $bid

                    UNION ALL

                    SELECT 22 AS jtid, a.suppid, a.no_inv, a.apdate, a.duedate
                        , (a.totalall - COALESCE(b.amount_pay, 0)) AS nominal
                        , (DATE('$sdate') - DATE(a.duedate)) AS up, a.doctor_id
                        , a.bid
                    FROM manual_ap a
                    LEFT JOIN (
                        SELECT x.maid, SUM(x.amount) AS amount_pay
                        FROM manual_ap_payment_d x
                        INNER JOIN manual_ap_payment y ON y.mapid = x.mapid
                        WHERE DATE(y.paydate) <= DATE('$sdate') AND y.bid = $bid
                        GROUP BY x.maid
                    ) b ON a.maid = b.maid
                    WHERE DATE(a.{$date_by}) <= DATE('$sdate') AND (a.totalall - COALESCE(b.amount_pay, 0)) <> 0
                        AND a.bid = $bid
                ) aa
                INNER JOIN m_supplier b ON b.suppid = aa.suppid
                INNER JOIN journal_type c ON c.jtid = aa.jtid
                LEFT JOIN person pd ON aa.doctor_id = pd.pid
                WHERE aa.bid = $bid $addsql
                ORDER BY b.nama_supp, nama_dokter, aa.{$date_by}";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>