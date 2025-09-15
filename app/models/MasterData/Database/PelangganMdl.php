<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PelangganMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function pelanggan_group () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT nama_group, gcustid FROM m_group_customer WHERE is_aktif = 't' ORDER BY gcustid";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $kode_nama_cust = strtolower(trim($data['kode_nama_cust']));
        $s_gcustid = $data['s_gcustid'];

        if ($kode_nama_cust) $addsql .= " AND LOWER(a.kode_customer || a.nama_customer) ILIKE '%$kode_nama_cust%'";

        if ($s_gcustid) $addsql .= " AND a.gcustid = ".$s_gcustid;

        $sql = "SELECT a.*, b.nama_group, (c.coacode || ' ' || c.coaname) AS coa_ar
                FROM m_customer a
                JOIN m_group_customer b ON b.gcustid = a.gcustid
                JOIN m_coa c ON c.coaid = a.coaid_ar
                WHERE 1 = 1 $addsql
                ORDER BY b.gcustid, a.nama_customer";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function pelanggan_detail ($custid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_customer WHERE custid = ?";
        $rs = DB::Execute($sql, array($custid));

        return $rs;
    } /*}}}*/

    public static function setup_coa_ar () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (b.coacode || ' - ' || b.coaname) AS coa, b.coaid FROM setup_coa a, m_coa b WHERE b.coaid = a.coaid AND a.is_aktif = 't' AND a.sctype = 3 ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM m_customer WHERE LOWER(kode_customer) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['custid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_pelanggan () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $custid = get_var('custid', 0);
        $gcustid = get_var('gcustid', NULL);
        $bid      = get_var('bid', NULL);
        $kode_customer = get_var('kode_customer');
        $nama_customer = get_var('nama_customer');
        $coaid_ar = get_var('coaid_ar', NULL);
        $alamat_customer = get_var('alamat_customer');
        $kota_customer = get_var('kota_customer');
        $telp_customer = get_var('telp_customer');
        $email_customer = get_var('email_customer');
        $keterangan = get_var('keterangan');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['bid']              = $bid;
        $record['gcustid']          = $gcustid;
        $record['kode_customer']    = $kode_customer;
        $record['nama_customer']    = $nama_customer;
        $record['coaid_ar']         = $coaid_ar;
        $record['alamat_customer']  = $alamat_customer;
        $record['kota_customer']    = $kota_customer;
        $record['telp_customer']    = $telp_customer;
        $record['email_customer']   = $email_customer;
        $record['keterangan']       = $keterangan;
        $record['is_aktif']         = $is_aktif;

        $sql = "SELECT * FROM m_customer WHERE custid = ?";
        $rs = DB::Execute($sql, array($custid));

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