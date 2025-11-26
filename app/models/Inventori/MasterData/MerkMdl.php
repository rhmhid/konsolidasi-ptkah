<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class MerkMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $s_kode_nama_merk = strtolower(trim($data['s_kode_nama_merk']));

        if ($s_kode_nama_merk) $addsql .= " AND LOWER(a.kode_merk || a.nama_merk) ILIKE '%$s_kode_nama_merk%'";

        $sql = "SELECT a.*
                FROM m_merk a
                WHERE 1 = 1 $addsql
                ORDER BY a.nama_merk";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function mek_detail ($mmid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_merk WHERE mmid = ?";
        $rs = DB::Execute($sql, array($mmid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM m_merk WHERE LOWER(kode_merk) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['mmid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_merk () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $mmid = get_var('mmid', 0);
        $kode_merk = get_var('kode_merk');
        $nama_merk = get_var('nama_merk');
        $keterangan = get_var('keterangan');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['kode_merk']    = $kode_merk;
        $record['nama_merk']    = $nama_merk;
        $record['keterangan']   = $keterangan;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM m_merk WHERE mmid = ?";
        $rs = DB::Execute($sql, array($mmid));

        if ($rs->EOF)
        {
            $record['create_by'] = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);
        }
        else
        {
            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);
        }

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            return 'Debug Sql Mode';
        }

        if ($ok)
        {
            DB::CommitTrans();
            return 'true';
        }
        else
        {
            $errmsg = "sql error : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/
}
?>