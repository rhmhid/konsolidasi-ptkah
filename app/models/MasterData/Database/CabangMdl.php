<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class CabangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function cek_kode ($type, $kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $id = get_var('id');
        $status = '';

        if ($type == 'tipe')
        {
            $sql = "SELECT * FROM branch_tipe WHERE LOWER(kode_tipe) = LOWER(?)";

            $pkey = 'btid';
        }
        elseif ($type == 'wilayah')
        {
            $sql = "SELECT * FROM branch_wilayah WHERE LOWER(kode_wilayah) = LOWER(?)";

            $pkey = 'bwid';
        }
        elseif ($type == 'branch')
        {
            $sql = "SELECT * FROM branch WHERE LOWER(branch_code) = LOWER(?)";

            $pkey = 'bid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$pkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function list_cabang ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $s_btid = $data['s_btid'];
        $s_bwid = $data['s_bwid'];
        $s_kode_nama_branch = strtolower(trim($data['s_kode_nama_branch']));

        if ($s_btid) $addsql .= " AND a.btid = ".$s_btid;

        if ($s_bwid) $addsql .= " AND a.bwid = ".$s_bwid;

        if ($s_kode_nama_branch) $addsql .= " AND LOWER(a.branch_code || a.branch_name) ILIKE '%$s_kode_nama_branch%'";

        $sql = "SELECT a.*, b.nama_wilayah AS wilayah, c.nama_tipe AS tipe
                FROM branch a
                INNER JOIN branch_wilayah b ON b.bwid = a.bwid
                INNER JOIN branch_tipe c ON c.btid = a.btid
                WHERE 1 = 1 $addsql
                ORDER BY b.nama_wilayah, a.is_primary DESC, a.branch_code";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_cabang ($bid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM branch WHERE bid = ?";
        $rs = DB::Execute($sql, array($bid));

        return $rs;
    } /*}}}*/

    public static function branch_tipe () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT nama_tipe, btid FROM branch_tipe WHERE is_aktif = 't' ORDER BY nama_tipe";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function branch_wilayah () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT nama_wilayah, bwid FROM branch_wilayah WHERE is_aktif = 't' ORDER BY nama_wilayah";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list_cabang_tipe ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT a.* FROM branch_tipe a ORDER BY a.kode_tipe";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function list_cabang_wilayah ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT a.* FROM branch_wilayah a ORDER BY a.kode_wilayah";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function save_cabang_tipe () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $btid = get_var('btid', 0);
        $kode_tipe = get_var('kode_tipe');
        $nama_tipe = get_var('nama_tipe');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM branch_tipe WHERE btid = ?";
        $rs = DB::Execute($sql, array($btid));

        $record = array();
        $record['kode_tipe']    = $kode_tipe;
        $record['nama_tipe']    = $nama_tipe;
        $record['is_aktif']     = $is_aktif;

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

    public static function save_cabang_wilayah () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bwid = get_var('bwid', 0);
        $kode_wilayah = get_var('kode_wilayah');
        $nama_wilayah = get_var('nama_wilayah');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM branch_wilayah WHERE bwid = ?";
        $rs = DB::Execute($sql, array($bwid));

        $record = array();
        $record['kode_wilayah'] = $kode_wilayah;
        $record['nama_wilayah'] = $nama_wilayah;
        $record['is_aktif']     = $is_aktif;

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

    public static function save_cabang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = get_var('bid', 0);
        $branch_code = get_var('branch_code');
        $branch_name = get_var('branch_name');
        $branch_sub_corp = get_var('branch_sub_corp');
        $btid = get_var('btid', NULL);
        $bwid = get_var('bwid', NULL);
        $branch_addr = get_var('branch_addr');
        $branch_desc = get_var('branch_desc');
        $coaid_branch = get_var('coaid_branch', NULL);
        $is_primary = get_var('is_primary', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $cabang_url = get_var('cabang_url');
        $cabang_user = get_var('cabang_user');
        $cabang_pass = get_var('cabang_pass');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM branch WHERE bid = ?";
        $rs = DB::Execute($sql, array($bid));

        $record = array();
        $record['branch_code']      = $branch_code;
        $record['branch_name']      = $branch_name;
        $record['branch_sub_corp']  = $branch_sub_corp;
        $record['btid']             = $btid;
        $record['bwid']             = $bwid;
        $record['branch_addr']      = $branch_addr;
        $record['branch_desc']      = $branch_desc;
        $record['coaid_branch']     = $coaid_branch;
        $record['is_primary']       = $is_primary;
        $record['is_aktif']         = $is_aktif;
        $record['cabang_url']       = $cabang_url;
        $record['cabang_user']      = $cabang_user;
        $record['cabang_pass']      = $cabang_pass;

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            // Khusus PID = 1, inject otomatis ketika nambah
            if ($ok)
            {
                $newbid = DB::GetOne("SELECT CURRVAL('branch_bid_seq') AS code");

                $sql = "SELECT * FROM branch_assign WHERE item_type = 1 AND base_id = ? AND bid = ?";
                $rs = DB::Execute($sql, [SUPER_USER, $newbid]);

                $record = array();
                $record['item_type']    = 1;
                $record['base_id']      = 1;
                $record['bid']          = $newbid;

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
                }
            }
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

    public static function cabang_by_assign ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT string_agg(bid::VARCHAR, ',') FROM branch_assign WHERE item_type = ? AND base_id = ?";
        $rs = DB::GetOne($sql, [$data['item_type'], $data['base_id']]);

        return $rs;
    } /*}}}*/

    public static function save_assign_branch () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $item_type = get_var('item_type', 0);
        $base_id = get_var('base_id');
        $bid = get_var('bid');

        $userid = Auth::user()->pid;

        DB::BeginTrans();

        if (!empty($bid))
        {
            $arr_bid = implodeData(',', $bid);
            foreach ($bid as $id => $val)
            {
                $sql = "SELECT * FROM branch_assign WHERE item_type = ? AND base_id = ? AND bid = ?";
                $rs = DB::Execute($sql, [$item_type, $base_id, $val]);

                $record = array();
                $record['item_type']    = $item_type;
                $record['base_id']      = $base_id;
                $record['bid']          = $val;

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
                }
            }

            $sql = "DELETE FROM branch_assign WHERE item_type = ? AND base_id = ? AND bid NOT IN ($arr_bid)";
            $ok = DB::Execute($sql, [$item_type, $base_id]);
        }
        else
        {
            $sql = "DELETE FROM branch_assign WHERE item_type = ? AND base_id = ?";
            $ok = DB::Execute($sql, [$item_type, $base_id]);
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