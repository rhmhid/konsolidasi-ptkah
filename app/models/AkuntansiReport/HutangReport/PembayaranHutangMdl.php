<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PembayaranHutangMdl extends DB
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
        $edate = date('Y-m-d', strtotime($data['edate']));
        $suppid = $data['suppid'];
        $doctor_id = $data['doctor_id'];

        if ($suppid) $addsql .= " AND aa.suppid = ".$suppid;

        if ($doctor_id) $addsql .= " AND aa.doctor_id = ".$doctor_id;

        $sql = "SELECT aa.suppid, ms.nama_supp, aa.bank_id, mb.bank_nama, aa.cara_bayar, aa.paydate
                    , aa.no_bayar, aa.keterangan, aa.pembayaran, aa.al_debet, aa.al_credit, aa.potongan
                    , aa.pembulatan, aa.other_cost, pd.nama_lengkap AS nama_dokter
                FROM (
                    /*SELECT a.suppid, a.bank_id, a.cara_bayar, a.paydate, a.no_bayar, a.keterangan
                        , SUM(b.nominal) AS pembayaran, c.al_debet, c.al_credit, a.potongan, a.pembulatan
                        , a.other_cost, a.doctor_id
                    FROM ap_payment a
                    INNER JOIN ap_payment_d b ON a.appid = b.appid
                    LEFT JOIN (
                        SELECT appid, SUM(debet) AS al_debet, SUM(credit) AS al_credit
                        FROM ap_payment_addless
                        GROUP BY appid
                    ) c ON a.appid = c.appid
                    WHERE DATE(a.paydate) BETWEEN DATE(?) AND DATE(?) AND a.bid = ?
                    GROUP BY a.suppid, a.bank_id, a.cara_bayar, a.paydate, a.no_bayar, a.keterangan
                        , c.al_debet, c.al_credit, a.potongan, a.pembulatan, a.other_cost, a.doctor_id

                    UNION ALL*/

                    SELECT a.suppid, a.bank_id, a.cara_bayar, a.paydate, a.no_bayar, a.keterangan
                        , SUM(b.amount) AS pembayaran, 0 AS al_debet, 0 AS al_credit, a.potongan
                        , a.pembulatan, a.other_cost, a.doctor_id
                    FROM manual_ap_payment a
                    INNER JOIN manual_ap_payment_d b ON a.mapid = b.mapid
                    WHERE DATE(a.paydate) BETWEEN DATE(?) AND DATE(?) AND a.bid = ?
                    GROUP BY a.suppid, a.bank_id, a.cara_bayar, a.paydate, a.no_bayar, a.keterangan
                        , a.potongan, a.pembulatan, a.other_cost, a.doctor_id
                ) aa
                INNER JOIN m_supplier ms ON ms.suppid = aa.suppid
                INNER JOIN m_bank mb ON mb.bank_id = aa.bank_id
                LEFT JOIN person pd ON aa.doctor_id = pd.pid
                WHERE 1 = 1 $addsql
                ORDER BY ms.nama_supp, aa.paydate";
        $rs = DB::Execute($sql, [$sdate, $edate, $bid, $sdate, $edate, $bid]);

        return $rs;
    } /*}}}*/
}
?>