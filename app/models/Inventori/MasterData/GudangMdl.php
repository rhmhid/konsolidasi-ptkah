<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class GudangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function jenis_gudang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT 'Gudang Besar (Pembelian dan Penerimaan Barang)' AS names, 'is_gudang_besar' AS fields, 1 AS id UNION
                SELECT 'Gudang Penjualan (Penjualan Barang)' AS names, 'is_sales' AS fields, 2 AS id UNION
                SELECT 'Depo (Gudang Kecil / Unit)' AS names, 'is_depo' AS fields, 3 AS id
                ORDER BY id";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $s_kode_nama_gudang = strtolower(trim($data['s_kode_nama_gudang']));
        $s_lokasi = strtolower(trim($data['s_lokasi']));
        $s_jenis_gudang = $data['s_jenis_gudang'];

        if ($s_kode_nama_gudang) $addsql .= " AND LOWER(a.kode_gudang || a.nama_gudang) ILIKE '%$s_kode_nama_gudang%'";

        if ($s_lokasi) $addsql .= " AND LOWER(a.lokasi) ILIKE '%$s_lokasi%'";

        if ($s_jenis_gudang) $addsql .= " AND a.{$s_jenis_gudang} = 't'";

        $sql = "SELECT a.*, (b.pcccode || ' - ' || b.pccname) AS cost_center
                FROM m_gudang a
                LEFT JOIN profit_cost_center b ON a.pccid = b.pccid
                WHERE a.gid > 0 $addsql
                ORDER BY a.nama_gudang";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function gudang_detail ($gid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_gudang WHERE gid > 0 AND gid = ?";
        $rs = DB::Execute($sql, array($gid));

        return $rs;
    } /*}}}*/

    public static function cek_kode () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $id = get_var('id');
        $kode = get_var('kode');
        $status = '';

        $sql = "SELECT * FROM m_gudang WHERE LOWER(kode_gudang) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['gid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_gudang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $gid = get_var('gid', 0);
        $kode_gudang = get_var('kode_gudang');
        $nama_gudang = get_var('nama_gudang');
        $pccid = get_var('pccid', NULL);
        $lokasi = get_var('lokasi');
        $is_gudang_besar = get_var('is_gudang_besar', 'f');
        $is_sales = get_var('is_sales', 'f');
        $is_depo = get_var('is_depo', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['kode_gudang']      = $kode_gudang;
        $record['nama_gudang']      = $nama_gudang;
        $record['pccid']            = $pccid;
        $record['lokasi']           = $lokasi;
        $record['is_gudang_besar']  = $is_gudang_besar;
        $record['is_sales']         = $is_sales;
        $record['is_depo']          = $is_depo;
        $record['is_aktif']         = $is_aktif;

        $sql = "SELECT * FROM m_gudang WHERE gid = ?";
        $rs = DB::Execute($sql, array($gid));

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