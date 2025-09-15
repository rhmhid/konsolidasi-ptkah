<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KartuPiutangMdl extends DB
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

        if ($custid) $addsql .= " AND gl.suppid = ".$custid;

        if ($pegawai_id) $addsql .= " AND gl.other_reff_id = ".$pegawai_id;

        if ($bank_id) $addsql .= " AND gl.other_reff_id = ".$bank_id;

        if ($status == 'saldo_awal')
        {
            $addselect = "SUM(gld.debet - gld.credit) AS saldo_awal";
            $addwhere = "AND DATE(gl.gldate) < '$sdate'";
            $addorder = "";
        }
        else
        {
            $addselect = "gl.jtid, jt.journal_name, gl.glid, gl.gldate, gld.notes,
                            (CASE WHEN gl.jtid IN (25, 27) THEN (gld.debet - gld.credit) END) AS nominal_inv,
                            (CASE WHEN gl.jtid IN (26, 28) THEN (gld.credit - gld.debet) END) AS nominal_pay";
            $addwhere = "AND DATE(gl.gldate) BETWEEN '$sdate' AND '$edate'";
            $addorder = "ORDER BY gl.gldate, gl.glid";
        }

        $sql = "SELECT $addselect
                FROM general_ledger_d gld
                INNER JOIN general_ledger gl ON gl.glid = gld.glid
                INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                WHERE gl.bid = $bid AND gl.jtid IN (25, 26, 27, 28, 29)
                    AND gld.gltype = (CASE WHEN gl.jtid IN (26, 28) THEN 1 ELSE 2 END)
                    $addwhere $addsql
                $addorder";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>