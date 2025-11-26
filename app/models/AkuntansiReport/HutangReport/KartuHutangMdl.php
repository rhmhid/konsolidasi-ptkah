<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KartuHutangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
       //  if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sdate = date('Y-m-d', strtotime($data['sdate']));
        $edate = date('Y-m-d', strtotime($data['edate']));
        $suppid = $data['suppid'];
        $doctor_id = $data['doctor_id'];
        $status = $data['status'];

        if ($suppid) $addsql .= " AND gl.suppid = ".$suppid;

        if ($doctor_id) $addsql .= " AND gl.other_reff_id = ".$doctor_id;

        if ($status == 'saldo_awal')
        {
            $addselect = "SUM(gld.credit - gld.debet) AS saldo_awal";
            $addwhere = "AND DATE(gl.gldate) < '$sdate'";
            $addorder = "";
        }
        else
        {
            $addselect = "gl.jtid, jt.journal_name, gl.glid, gl.gldate, gld.notes,gl.gldesc,
                            (CASE WHEN gl.jtid IN (20, 22) THEN (gld.credit - gld.debet) END) AS nominal_inv,
                            (CASE WHEN gl.jtid IN (21, 23) THEN (gld.debet - gld.credit) END) AS nominal_pay";
            $addwhere = "AND DATE(gl.gldate) BETWEEN '$sdate' AND '$edate'";
            $addorder = "ORDER BY gl.gldate, gl.glid";
        }

        $sql = "SELECT $addselect
                FROM general_ledger_d gld
                INNER JOIN general_ledger gl ON gl.glid = gld.glid
                INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                WHERE gl.bid = $bid AND gl.jtid IN (20, 21, 22, 23) AND gld.gltype = (CASE WHEN gl.jtid IN (21, 23) THEN 1 ELSE 2 END)
                    $addwhere $addsql
                $addorder";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/
}
?>
