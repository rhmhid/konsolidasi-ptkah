<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KategoriBarangMdl extends DB
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
        $s_kode_nama_kate = strtolower(trim($data['s_kode_nama_kate']));

        if ($s_kode_nama_kate) $addsql .= " AND LOWER(a.kode_kategori || a.nama_kategori) ILIKE '%$s_kode_nama_kate%'";

        $sql = "SELECT a.*, (b.coacode || ' - ' || b.coaname) AS coa_inv,
                    (c.coacode || ' - ' || c.coaname) AS coa_cogs
                FROM m_kategori_barang a
                LEFT JOIN m_coa b ON a.coaid_inv = b.coaid
                LEFT JOIN m_coa c ON a.coaid_cogs = c.coaid
                WHERE 1 = 1 $addsql
                ORDER BY a.nama_kategori";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function kategori_barang_detail ($kbid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_kategori_barang WHERE kbid = ?";
        $rs = DB::Execute($sql, array($kbid));

        return $rs;
    } /*}}}*/

    public static function data_coa ($coatid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' - ' || a.coaname) AS coa, a.coaid
                FROM m_coa a
                WHERE a.is_valid = 't' AND a.coaid NOT IN (SELECT coaid FROM default_coa WHERE default_code IN ('RETAINEDEARNING_ACCT', 'INCOMESUMMARY_ACCT'))
                    AND a.allow_post = 't' AND a.coatid = ?
                ORDER BY coa";
        $rs = DB::Execute($sql, array($coatid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM m_kategori_barang WHERE LOWER(kode_kategori) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['kbid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_kategori_barang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $kbid = get_var('kbid', 0);
        $kode_kategori = get_var('kode_kategori');
        $nama_kategori = get_var('nama_kategori');
        $format_kode_brg = get_var('format_kode_brg');
        $length_format_kode_brg = get_var('length_format_kode_brg');
        $coaid_inv = get_var('coaid_inv', NULL);
        $coaid_sales = get_var('coaid_sales', NULL);
        $coaid_sales_inpatient = get_var('coaid_sales_inpatient', NULL);
        $coaid_cogs = get_var('coaid_cogs', NULL);
        $coaid_cogs_inpatient = get_var('coaid_cogs_inpatient', NULL);
        $coaid_adj = get_var('coaid_adj', NULL);
        $coaid_so = get_var('coaid_so', NULL);
        $coaid_ciu = get_var('coaid_ciu', NULL);
        $coaid_cogs_ap_konsinyasi = get_var('coaid_cogs_ap_konsinyasi', NULL);
        $is_medis = get_var('is_medis', 'f');
        $is_freeze = get_var('is_freeze', 'f');
        $is_sales = get_var('is_sales', 'f');
        $is_fixed_asset = get_var('is_fixed_asset', 'f');
        $is_konsinyasi = get_var('is_konsinyasi', 'f');
        $is_service = get_var('is_service', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record['kode_kategori']            = $kode_kategori;
        $record['nama_kategori']            = $nama_kategori;
        $record['format_kode_brg']          = $format_kode_brg;
        $record['length_format_kode_brg']   = $length_format_kode_brg;
        $record['coaid_inv']                = $coaid_inv;
        $record['coaid_sales']              = $coaid_sales;
        $record['coaid_sales_inpatient']    = $coaid_sales_inpatient;
        $record['coaid_cogs']               = $coaid_cogs;
        $record['coaid_cogs_inpatient']     = $coaid_cogs_inpatient;
        $record['coaid_adj']                = $coaid_adj;
        $record['coaid_so']                 = $coaid_so;
        $record['coaid_ciu']                = $coaid_ciu;
        $record['coaid_cogs_ap_konsinyasi'] = $coaid_cogs_ap_konsinyasi;
        $record['is_medis']                 = $is_medis;
        $record['is_freeze']                = $is_freeze;
        $record['is_sales']                 = $is_sales;
        $record['is_fixed_asset']           = $is_fixed_asset;
        $record['is_konsinyasi']            = $is_konsinyasi;
        $record['is_service']               = $is_service;
        $record['is_aktif']                 = $is_aktif;

        $sql = "SELECT * FROM m_kategori_barang WHERE kbid = ?";
        $rs = DB::Execute($sql, array($kbid));

        if ($rs->EOF)
        {
            $record['create_by'] = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);
        }
        else
        {
            if ($rs->fields['coaid_inv'] != '' && $rs->fields['coaid_inv'] != $coaid_inv)
            {
                $cek_data = DB::GetOne("SELECT COUNT(a.invdid)
                                        FROM inventory_d a
                                        INNER JOIN m_barang b ON b.mbid = a.mbid
                                        WHERE b.kbid = ? AND a.coaid = ?", [$kbid, $rs->fields['coaid_inv']]);

                if ($cek_data > 0)
                {
                    DB::RollbackTrans();

                    return 'C.O.A Inventory Sudah Digunakan, Tidak Bisa Dirubah';
                }
            }

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