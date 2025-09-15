<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KasBankMdl extends DB
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
        $bank_id = $data['bank_id'];
        $pctid = $data['pctid'];
        $jtid = $data['jtid'];
        $is_posted = $data['is_posted'];
        $status = $data['status'];

        if ($bank_id) $addsql .= " AND mb.bank_id = ".$bank_id;

        if ($jtid) $addsql .= " AND gl.jtid = ".$jtid;

        if ($is_posted) $addsql .= " AND gl.is_posted = '$is_posted'";

        if ($status == 'saldo_awal')
        {
            $addselect = "SUM(gld.debet - gld.credit) AS saldo_awal";
            $addwhere = "AND DATE(gl.gldate) < '$sdate'";
            $addorder = "";
        }
        else
        {
            $addselect = "gl.glid, gl.gldate, gl.reff_code, gl.jtid, jt.journal_name, gld.notes
                        , gl.is_posted, format_glcode(gl.gldate, jt.doc_code, gl.gldoc, gl.glid) AS gldoc
                        , ps.nama_lengkap AS user_posting, (gld.debet - gld.credit) AS nominal";
            $addwhere = "AND DATE(gl.gldate) BETWEEN DATE('$sdate') AND DATE('$edate')";
            $addorder = "ORDER BY gl.gldate, gl.glid";
        }

        $sql = "SELECT $addselect
                FROM general_ledger_d gld
                INNER JOIN general_ledger gl ON gl.glid = gld.glid
                INNER JOIN journal_type jt ON jt.jtid = gl.jtid
                LEFT JOIN person ps ON gl.modify_by = ps.pid
                INNER JOIN m_bank mb ON mb.default_coaid = gld.coaid
                WHERE gl.bid = ? $addwhere
                    $addsql
                $addorder";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/
}
?>