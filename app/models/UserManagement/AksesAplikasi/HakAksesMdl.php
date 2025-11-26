<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class HakAksesMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        //if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $kode_nama_user = strtolower(trim($data['kode_nama_user']));

        // Khusus selain admin, tidak bisa ubah user is_admin = true
        if (Auth::user()->pid != 1) $addsql .= " AND a.is_admin = 'f'";

        if ($kode_nama_user) $addsql .= " AND LOWER(b.nrp || b.nama_lengkap || a.username) ILIKE '%$kode_nama_user%'";

        $sql = "SELECT b.pid, b.nrp, b.nama_lengkap, a.username,
                     b.is_aktif, a.asid, a.is_active
                FROM app_users a
                INNER JOIN person b ON b.pid = a.pid
                WHERE b.pid > 1 $addsql
                ORDER BY b.nama_lengkap";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function cari_pegawai () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $key = get_var("q");

        $sql = "SELECT a.pid, a.nrp, a.nama_lengkap
                FROM person a
                LEFT JOIN app_users b ON a.pid = b.pid
                WHERE a.pid > 1 AND b.asid ISNULL AND LOWER(a.nrp || a.nama_lengkap) ILIKE LOWER('%$key%')
                ORDER BY a.nama_lengkap";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function cek_user ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        if ($jenis == 'akses_user') // Cek Kode Group Akses
        {
            $sql = "SELECT * FROM app_users WHERE LOWER(username) = LOWER(?)";

            $fields = 'pid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$fields] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function list_gudang_aktif () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_gudang WHERE is_aktif = 't' AND gid > 0 ORDER BY nama_gudang";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_otorisasi_aktif () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM otorisasi_group WHERE is_aktif = 't' ORDER BY otogid";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_approval_aktif () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.lilid, a.urutan, b.lid, b.level_kode, b.level_name, c.lgid, c.lg_kode, c.lg_name, c.range_min, c.range_max,
                    d.ltid, d.lt_kode, d.lt_name
                FROM app_level_list a
                INNER JOIN app_level b ON b.lid = a.lid AND b.is_aktif = 't'
                INNER JOIN app_level_group c ON c.lgid = a.lgid AND b.is_aktif = 't'
                INNER JOIN app_level_type d ON d.ltid = c.ltid AND d.is_aktif = 't'
                WHERE a.is_aktif = 't'
                ORDER BY d.ltid, a.urutan";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_group_akses_aktif () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM role_group WHERE is_aktif = 't' ORDER BY role_name";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_hak_akses () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = get_var('user_pid', 0);
        $username = get_var('login_user');
        $userpass = get_var('login_pass');
        $reset_method = get_var('reset_method', 'f');
        $arr_rgid = get_var('rgid');
        $user_gudang = get_var('user_gudang');
        $user_otorisasi = get_var('user_otorisasi');
        $user_approval = get_var('user_approval');
        $is_admin = get_var('is_admin', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;
        $user_group = '';

        if (is_array($arr_rgid))
        {
            foreach ($arr_rgid as $rgid)
            {
                if ($mulai) $koma = ',';

                $user_group .= $koma.$rgid;
                $mulai = true;
            }
        }

        DB::BeginTrans();

        if ($reset_method == 't') $userpass = generateRandomKode(8);

        $record = array();
        $record['username']         = $username;
        $record['userpass']         = CreatePassword($userpass);
        $record['clue']             = encrypt($userpass);
        $record['is_admin']         = $is_admin;
        $record['is_active']        = $is_aktif;
        $record['user_group']       = $user_group;
        $record['pid']              = $pid;
        $record['user_gudang']      = $user_gudang ? implode(',', $user_gudang) : NULL;
        $record['user_otorisasi']   = $user_otorisasi ? implode(',', $user_otorisasi) : NULL;
        $record['user_approval']    = $user_approval ? implode(',', $user_approval) : NULL;
        $record['create_by']        = $record['modify_by'] = $userid;

        $sql = "SELECT * FROM app_users WHERE 1 = 2";
        $rs = DB::Execute($sql);
        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

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

    public static function hak_akses_detail ($pid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*, b.nama_lengkap FROM app_users a, person b WHERE b.pid = a.pid AND a.pid = ?";
        $rs = DB::Execute($sql, array($pid));

        return $rs;
    } /*}}}*/

    public static function update_hak_akses () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = get_var('user_pid', 0);
        $username = get_var('login_user');
        $arr_rgid = get_var('rgid');
        $user_gudang = get_var('user_gudang');
        $user_otorisasi = get_var('user_otorisasi');
        $user_approval = get_var('user_approval');
        $is_admin = get_var('is_admin', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;
        $user_group = '';

        if (is_array($arr_rgid))
        {
            foreach ($arr_rgid as $rgid)
            {
                if ($mulai) $koma = ',';

                $user_group .= $koma.$rgid;
                $mulai = true;
            }
        }

        DB::BeginTrans();

        $record = array();
        $record['username']         = $username;
        $record['is_admin']         = $is_admin;
        $record['is_active']        = $is_aktif;
        $record['user_group']       = $user_group;
        $record['user_gudang']      = $user_gudang ? implode(',', $user_gudang) : NULL;
        $record['user_otorisasi']   = $user_otorisasi ? implode(',', $user_otorisasi) : NULL;
        $record['user_approval']    = $user_approval ? implode(',', $user_approval) : NULL;
        $record['modify_by']        = $userid;
        $record['modify_time']      = 'NOW()';

        $sql = "SELECT * FROM app_users WHERE pid = ?";
        $rs = DB::Execute($sql, array($pid));
        $sqlu = DB::UpdateSQL($rs, $record);
        $ok = DB::Execute($sqlu);

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

    public static function update_pass () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        DB::BeginTrans();

        $pid = get_var('user_pid', 0);
        $userpass = get_var('login_pass');
        $reset_method = get_var('reset_method', 'f');
        $userid = Auth::user()->pid;

        if ($reset_method == 't') $userpass = generateRandomKode(8);

        $record = array();
        $record['userpass']     = CreatePassword($userpass);
        $record['clue']         = encrypt($userpass);
        $record['modify_by']    = $userid;
        $record['modify_time']  = 'NOW()';

        $sql = "SELECT * FROM app_users WHERE pid = ?";
        $rs = DB::Execute($sql, array($pid));
        $sqlu = DB::UpdateSQL($rs, $record);
        $ok = DB::Execute($sqlu);

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