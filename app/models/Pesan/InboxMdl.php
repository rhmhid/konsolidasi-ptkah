<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class InboxMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function get_notif ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql         = "";
        $addorder       = "";
        $first_new      = $data['first_new'];
        $notification   = $data['notification'];
        $pid            = Auth::user()->pid;

        if ($first_new)
            $addorder = 'a.create_time DESC';
        else
            $addorder = 'a.create_time';

        $sql = "SELECT a.*, b.nama_jenis
                FROM laporan a
                INNER JOIN jenis_laporan b ON b.jlid = a.jlid
                LEFT JOIN (SELECT lid, MIN(lsid) AS min_lsid FROM laporan_status GROUP BY lid) c ON a.lid = c.lid
                WHERE /*a.create_time > NOW() - INTERVAL '10 second'*/ 
                    (CASE WHEN c.lid ISNULL THEN 'f' ELSE 't' END) = 'f' $addsql
                ORDER BY $addorder";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function list_myinbox ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql         = "";
        $addorder       = "";
        $first_new      = $data['first_new'];
        $notification   = $data['notification'];
        $pid            = Auth::user()->pid;

        if ($first_new)
            $addorder = 'a.create_time DESC';
        else
            $addorder = 'a.create_time';

        $sql = "SELECT a.*, b.nama_jenis, (CASE WHEN c.lid ISNULL THEN 'f' ELSE 't' END) AS lapor_verif
                FROM laporan a
                INNER JOIN jenis_laporan b ON b.jlid = a.jlid
                LEFT JOIN (SELECT lid, MIN(lsid) AS min_lsid FROM laporan_status GROUP BY lid) c ON a.lid = c.lid
                WHERE 1 = 1 $addsql
                ORDER BY $addorder";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/
}
?>