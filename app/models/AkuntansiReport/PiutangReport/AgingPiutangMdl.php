<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class AgingPiutangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
<<<<<<< Updated upstream
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);
=======
   //              if (Auth::user()->pid == SUPER_USER) DB::Debug(true);
>>>>>>> Stashed changes

        $bid = Auth::user()->branch->bid;

        $sdate = date('Y-m-d', strtotime($data['sdate']));
        $custid = $data['custid'];
<<<<<<< Updated upstream
        $bank_id = $data['bank_id'];
        $pegawai_id = $data['pegawai_id'];
=======
        $pegawai_id = $data['pegawai_id'];
        $bank_id = $data['bank_id'];
>>>>>>> Stashed changes

        if ($custid) $addsql .= " AND aa.custid = ".$custid;
        if ($bank_id !='') $addsql .= " AND aa.bank_id = ".$bank_id;
        if ($pegawai_id) $addsql .= " AND aa.pegawai_id = ".$pegawai_id;

        if ($bank_id) $addsql .= " AND aa.bank_id = ".$bank_id;

        if ($pegawai_id) $addsql .= " AND aa.pegawai_id = ".$pegawai_id;

        $sql = "SELECT aa.jtid, c.journal_name, aa.custid, b.nama_customer, aa.no_inv, e.nama_lengkap
                    , aa.pegawai_id, aa.bank_id, d.bank_nama, aa.ardate, aa.duedate, aa.nominal, aa.up, aa.bid
                FROM (
                    SELECT 27 AS jtid, a.custid, a.no_inv, a.ardate, a.duedate, a.bank_id, a.pegawai_id
                        , (a.totalall - COALESCE(b.amount_pay, 0)) AS nominal
                        , (DATE('$sdate') - DATE(a.duedate)) AS up, a.bid
                    FROM manual_ar a
                    LEFT JOIN (
                        SELECT x.maid, SUM(x.amount) AS amount_pay
                        FROM manual_ar_payment_d x
                        INNER JOIN manual_ar_payment y ON y.mapid = x.mapid
                        WHERE DATE(y.paydate) <= DATE('$sdate') AND y.bid = $bid
                        GROUP BY x.maid
                    ) b ON a.maid = b.maid
                    WHERE DATE(a.ardate) <= DATE('$sdate') AND (a.totalall - COALESCE(b.amount_pay, 0)) <> 0
                        AND a.bid = $bid
                ) aa
                INNER JOIN m_customer b ON b.custid = aa.custid
                INNER JOIN journal_type c ON c.jtid = aa.jtid
                LEFT JOIN m_bank d ON aa.bank_id = d.bank_id
                LEFT JOIN person e ON aa.pegawai_id = e.pid
                WHERE aa.bid = $bid $addsql
                ORDER BY b.nama_customer, aa.ardate";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>
