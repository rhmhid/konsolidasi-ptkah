<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class SatuanMdl extends DB
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
        $s_kode_nama_sat = strtolower(trim($data['s_kode_nama_sat']));

        if ($s_kode_nama_sat) $addsql .= " AND LOWER(kode_satuan || nama_satuan) ILIKE '%$s_kode_nama_sat%'";

        $sql = "SELECT * FROM m_satuan WHERE 1 = 1 $addsql ORDER BY nama_satuan";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function satuan_detail ($msid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_satuan WHERE msid = ?";
        $rs = DB::Execute($sql, array($msid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        if ($jenis == 'sat')
        {
            $sql = "SELECT * FROM m_satuan WHERE LOWER(kode_satuan) = LOWER(?)";

            $pkey = 'msid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$pkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_satuan () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $msid = get_var('msid', 0);
        $kode_satuan = get_var('kode_satuan');
        $nama_satuan = get_var('nama_satuan');
        $keterangan = get_var('keterangan');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['kode_satuan']	= $kode_satuan;
        $record['nama_satuan']	= $nama_satuan;
        $record['keterangan']  	= $keterangan;
        $record['is_aktif']		= $is_aktif;

        $sql = "SELECT * FROM m_satuan WHERE msid = ?";
        $rs = DB::Execute($sql, array($msid));

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