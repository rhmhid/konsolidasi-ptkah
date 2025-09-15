<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class AuthMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function save_change_password () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $username = DB::qStr(get_var('username'));
        $userpass_lama = get_var('userpass_lama');
        $userpass_baru = get_var('userpass_baru');
        $userpass_confirm = get_var('userpass_confirm');
        $pid = Auth::user()->pid;

        $sql = "SELECT COUNT(asid) AS jml FROM app_users WHERE username = $username AND pid <> ".$pid;
        $check_user = DB::GetOne($sql);

        if ($check_user > 0)
        {
            $errmsg = "Username sudah digunakan user lain.";

            return $errmsg;
        }

        $sql = "SELECT userpass FROM app_users WHERE pid = ".$pid;
        $pass_db = DB::GetOne($sql);

        if (!CheckPassword($userpass_lama, $pass_db))
        {
            $errmsg = "Password lama tidak sesuai.";

            return $errmsg;
        }
        else
        {
            if (CheckPassword($userpass_baru, $pass_db))
            {
                $errmsg = "Password baru tidak boleh sama dengan password lama.";

                return $errmsg;
            }
        }

        DB::BeginTrans();

        $record = array();
        $record['userpass']     = CreatePassword($userpass_baru);
        $record['clue']         = encrypt($userpass_baru);
        $record['modify_by']    = $pid;
        $record['modify_time']  = 'NOW()';

        $sql = "SELECT * FROM app_users WHERE pid = ?";
        $rs = DB::Execute($sql, array($pid));
        $newsql = DB::UpdateSQL($rs, $record);
        $ok = DB::Execute($newsql);

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

    public static function get_otorisasi_by_user ($otogid, $pid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.otoid FROM otorisasi a WHERE a.otogid = ? AND a.pid = ?";
        $rs = DB::GetOne($sql, array($otogid, $pid));

        return $rs;
    } /*}}}*/

    public static function get_otorisasi_group_by_user ($otogid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.subtitle FROM otorisasi_group a WHERE a.otogid = ?";
        $rs = DB::GetOne($sql, array($otogid));

        return $rs;
    } /*}}}*/

    public static function get_user_by_otorisasi ($otogid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT b.nama_lengkap, a.otoid
                FROM otorisasi a
                INNER JOIN person b ON b.pid = a.pid
                WHERE a.otogid = ?
                ORDER BY UPPER(b.nama_lengkap)";
        $rs = DB::Execute($sql, array($otogid));

        return $rs;
    } /*}}}*/

    public static function save_otorisasi_akses () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        DB::BeginTrans();

        $otogid = get_var('otogid');
        $otoid = get_var('otoid');
        $pid = DB::GetOne("SELECT pid FROM otorisasi WHERE otoid = ?", array($otoid));
        $pass_user = get_var('pass_user');;
        $notes = get_var('notes');
        $keterangan_auth = get_var('keterangan_auth');
        $userid = Auth::user()->pid;

        // Cek User
        if ($pid == '')
        {
            $errmsg = "User Tidak Ditemukan.";

            DB::RollbackTrans();
            return $errmsg;
        }
        else
        {
            $sql = "SELECT userpass FROM app_users WHERE pid = ?";
            $pass_db = DB::GetOne($sql, array($pid));
        }

        if (!CheckPassword($pass_user, $pass_db))
        {
            $errmsg = "Password Tidak Sesuai. Silahkan Cek Kembali.";

            DB::RollbackTrans();
            return $errmsg;
        }

        $record = array();
        $record['otoid']        = $otoid;
        $record['pid']          = $pid;
        $record['otogid']       = $otogid;
        $record['description']  = $notes;
        $record['alasan']       = $keterangan_auth;
        $record['create_by']    = $userid;
        $record['modify_by']    = $userid;

        $sql = "SELECT * FROM otorisasi_logs WHERE 1 = 2";
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

    public static function save_change_branch () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        DB::BeginTrans();

        $bid = get_var('branch_id_mdl');
        $userid = Auth::user()->pid;

        $sql = "UPDATE app_users SET last_login_bid = $bid  WHERE pid = ?";
        $ok = DB::Execute($sql, [$userid]);

        if ($ok)
        {
            $sqlb = "SELECT branch_name, branch_addr, branch_logo FROM branch WHERE bid = ?";
            $rs_branch = DB::Execute($sqlb, [$bid]);

            $data_branch = array(
                'bid'           => $bid,
                'branch_name'   => $rs_branch->fields['branch_name'],
                'branch_addr'   => $rs_branch->fields['branch_addr'],
                'branch_logo'   => $rs_branch->fields['branch_logo'],
            );

            $_SESSION[config_item('auth_session_var')]['user']['entity']->last_login_bid = $bid;
            $_SESSION[config_item('auth_session_var')]['user']['entity']->branch = FieldsToObject($data_branch);
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