<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PegawaiMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
     //    if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $kode_nama_emp = strtolower(trim($data['kode_nama_emp']));

        if ($kode_nama_emp) $addsql .= " AND LOWER(a.nrp || a.nama_lengkap) ILIKE '%$kode_nama_emp%'";

        $sql = "SELECT a.*
                FROM person a
                WHERE a.pid > 1 $addsql
                ORDER BY a.nama_lengkap";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_pegawai ($pid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*
                FROM person a
                WHERE a.pid > 1 AND a.pid = ?";
        $rs = DB::Execute($sql, array($pid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        if ($jenis == 'emp_nip')
        {
            $sql = "SELECT * FROM person WHERE LOWER(nip) = LOWER(?)";

            $pkey = 'pid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$pkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_pegawai () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = get_var('pid', 0);
        $nrp = get_var('nrp');
        $nama_lengkap        = get_var('nama_lengkap');
        $jenis_kelamin       = get_var('sex');
        $tempat_lahir        = get_var('tempat_lahir');
        $tanggal_lahir           = get_var('tanggal_lahir', NULL);
        $alamat_lengkap      = get_var('alamat_lengkap');
        $mjid                = get_var('mjid');
        $divid               = get_var('divid');
        $mtid                = get_var('mtid');

        $ptype = get_var('ptype', NULL);
        $is_aktif = get_var('is_aktif', 'f');
        $is_dokter = get_var('is_dokter', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['nrp']                  = $nrp;
        $record['no_ktp']               = $no_ktp;
        $record['nama_lengkap']         = $nama_lengkap;
        $record['jenis_kelamin']        = $jenis_kelamin;
        $record['tempat_lahir']         = $tempat_lahir;
        $record['tanggal_lahir']        = $tanggal_lahir;
        $record['alamat_lengkap']       = $alamat_lengkap;
        $record['mjid']                 = $mjid;
        $record['divid']                = $divid;
        $record['mtid']                 = $mtid;
        $record['ptype']                = $ptype;
        $record['is_aktif']             = $is_aktif;
        $record['is_dokter']            = $is_dokter;

        $sql = "SELECT * FROM person WHERE pid = ?";
        $rs = DB::Execute($sql, array($pid));

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);
        }
        else
        {
            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);

            if ($ok)
            {
                $chek_user = DB::GetOne("SELECT pid FROM app_users WHERE pid = ?", array($pid));

                if ($chek_user != '')
                {
                    $sql = "UPDATE app_users SET is_active = '$is_aktif', non_aktif_time = NOW(), non_aktif_by = $userid WHERE pid = ?";
                    $ok = DB::Execute($sql, array($pid));
                }
            }
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
