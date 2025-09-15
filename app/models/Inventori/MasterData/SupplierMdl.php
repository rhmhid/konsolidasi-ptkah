<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class SupplierMdl extends DB
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
        $s_type_supp = $data['s_type_supp'];
        $kode_nama_supp = strtolower(trim($data['kode_nama_supp']));

        if ($s_type_supp) $addsql .= " AND a.type_supp = ".$s_type_supp;

        if ($kode_nama_supp) $addsql .= " AND LOWER(a.kode_supp || a.nama_supp) ILIKE '%$kode_nama_supp%'";

        $sql = "SELECT a.*, (b.coacode || ' - ' || b.coaname) AS coa_ap
                FROM m_supplier a
                LEFT JOIN m_coa b ON a.coaid_ap = b.coaid
                WHERE a.suppid > 0 $addsql
                ORDER BY a.nama_supp";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function supplier_detail ($suppid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_supplier WHERE suppid = ?";
        $rs = DB::Execute($sql, array($suppid));

        return $rs;
    } /*}}}*/

    public static function setup_coa_ap () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (b.coacode || ' - ' || b.coaname) AS coa, b.coaid FROM setup_coa a, m_coa b WHERE b.coaid = a.coaid AND a.is_aktif = 't' AND a.sctype = 1 ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM m_supplier WHERE LOWER(kode_supp) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['suppid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_supplier () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $suppid = get_var('suppid', 0);
        $kode_supp = get_var('kode_supp');
        $nama_supp = get_var('nama_supp');
        $type_supp = get_var('type_supp');
        $addr_supp = get_var('addr_supp');
        $kota_supp = get_var('kota_supp');
        $kode_pos = get_var('kode_pos');
        $kontak_supp = get_var('kontak_supp');
        $telp = get_var('telp');
        $fax = get_var('fax');
        $email_supp = get_var('email_supp');
        $bank = get_var('bank');
        $atas_nama = get_var('atas_nama');
        $no_rek = get_var('no_rek');
        $npwp = get_var('npwp');
        $keterangan = get_var('keterangan');
        $coaid_ap = get_var('coaid_ap', NULL);
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['kode_supp']    = $kode_supp;
        $record['nama_supp']    = $nama_supp;
        $record['type_supp']     = $type_supp;
        $record['addr_supp']    = $addr_supp;
        $record['kota_supp']    = $kota_supp;
        $record['kode_pos']     = $kode_pos;
        $record['kontak_supp']  = $kontak_supp;
        $record['telp']         = $telp;
        $record['fax']          = $fax;
        $record['email_supp']   = $email_supp;
        $record['bank']         = $bank;
        $record['atas_nama']    = $atas_nama;
        $record['no_rek']       = $no_rek;
        $record['npwp']         = $npwp;
        $record['keterangan']   = $keterangan;
        $record['coaid_ap']     = $coaid_ap;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM m_supplier WHERE suppid = ?";
        $rs = DB::Execute($sql, array($suppid));

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
}
?>