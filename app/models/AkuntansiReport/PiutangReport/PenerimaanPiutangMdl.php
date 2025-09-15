<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PenerimaanPiutangMdl extends DB
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
        $custid = $data['custid'];
        $pegawai_id = $data['pegawai_id'];
        $bank_id = $data['bank_id'];
        $status = $data['status'];

        if ($custid) $addsql .= " AND aa.custid = ".$custid;

        if ($pegawai_id) $addsql .= " AND aa.pegawai_id = ".$pegawai_id;

        if ($bank_id) $addsql .= " AND aa.bank_id = ".$bank_id;

        $sql = "SELECT aa.custid, ms.nama_customer, aa.bank_id, mb.bank_nama, aa.cara_terima, aa.paydate
                    , aa.paycode, aa.keterangan, aa.penerimaan, aa.potongan, aa.pembulatan, aa.other_cost
                    , aa.pegawai_id, ps.nama_lengkap, aa.bank_ar, ba.bank_nama AS bn_ar
                FROM (
                    -- SELECT a.custid, a.bank_id, a.cara_terima, a.paydate, a.paycode, a.keterangan
                    --     , SUM(b.amount) AS penerimaan, a.potongan, a.pembulatan, a.other_cost, NULL AS pegawai_id
                    --     , NULL AS bank_ar, a.bid
                    -- FROM ar_payment a
                    -- INNER JOIN ar_payment_d b ON a.arpid = b.arpid
                    -- WHERE DATE(a.paydate) BETWEEN DATE(?) AND DATE(?)
                    -- GROUP BY a.custid, a.bank_id, a.cara_terima, a.paydate, a.paycode, a.keterangan
                    --     , a.potongan, a.pembulatan, a.other_cost, a.bid

                    -- UNION ALL

                    SELECT a.custid, a.bank_id, a.cara_terima, a.paydate, a.paycode, a.keterangan
                        , (SUM(b.amount) - COALESCE(SUM(c.debet), 0) + COALESCE(SUM(c.credit), 0)) AS penerimaan
                        , a.potongan, a.pembulatan, a.other_cost, a.pegawai_id, a.bank_ar, a.bid
                    FROM manual_ar_payment a
                    INNER JOIN manual_ar_payment_d b ON a.mapid = b.mapid
                    LEFT JOIN manual_ar_payment_addless c ON a.mapid = c.mapid
                    WHERE DATE(a.paydate) BETWEEN DATE(?) AND DATE(?)
                    GROUP BY a.custid, a.bank_id, a.cara_terima, a.paydate, a.paycode, a.keterangan
                        , a.potongan, a.pembulatan, a.other_cost, a.pegawai_id, a.bank_ar, a.bid
                ) aa
                INNER JOIN m_customer ms ON ms.custid = aa.custid
                INNER JOIN m_bank mb ON mb.bank_id = aa.bank_id
                LEFT JOIN m_bank ba ON aa.bank_ar = ba.bank_id
                LEFT JOIN person ps ON aa.pegawai_id = ps.pid
                WHERE aa.bid = ? $addsql
                ORDER BY ms.nama_customer, aa.paydate";
        $rs = DB::Execute($sql, [$sdate, $edate, $sdate, $edate, $bid]);

        return $rs;
    } /*}}}*/
}
?>