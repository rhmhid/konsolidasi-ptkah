<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KasbankMdl extends DB
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
        $s_bank_type = $data['s_bank_type'];
        $s_kas_bank = strtolower(trim($data['s_kas_bank']));
        $s_coaid = $data['s_coaid'];

        if ($s_bank_type) $addsql .= " AND a.bank_type = ".$s_bank_type;

        if ($s_kas_bank) $addsql .= " AND LOWER(a.bank_kode || a.bank_nama) ILIKE ('%$s_kas_bank%')";

        if ($s_coaid) $addsql .= " AND a.default_coaid = ".$s_coaid;

        $sql = "SELECT a.bank_id, b.bank_type, a.bank_nama, a.bank_no_rek,
                    (c.coacode || ' - ' || c.coaname) AS default_coa,
                    (d.coacode || ' - ' || d.coaname) AS ctrl_acc_coaid,
                    a.bank_atas_nama, a.bank_cabang, a.is_aktif
                FROM m_bank a
                INNER JOIN m_bank_type b ON b.mbtid = a.bank_type
                INNER JOIN m_coa c ON c.coaid = a.default_coaid
                LEFT JOIN m_coa d ON a.ctrl_acc_coaid = d.coaid
                WHERE 1 = 1 $addsql
                ORDER BY a.bank_nama";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function bank_type () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT bank_type, mbtid FROM m_bank_type WHERE is_aktif = 't' ORDER BY mbtid";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function bank_image () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT 'BCA' AS name, 'bca.png' AS id UNION
                SELECT 'BNI' AS name, 'bni.png' AS id UNION
                SELECT 'BRI' AS name, 'bri.png' AS id UNION
                SELECT 'Citi Bank' AS name, 'citibank.png' AS id UNION
                SELECT 'Commonwealth' AS name, 'commonwealth.png' AS id UNION
                SELECT 'Dana' AS name, 'dana.png' AS id UNION
                SELECT 'Danamon' AS name, 'danamon.png' AS id UNION
                SELECT 'Gopay' AS name, 'gopay.png' AS id UNION
                SELECT 'Link Aja' AS name, 'linkaja.png' AS id UNION
                SELECT 'Mandiri' AS name, 'mandiri.png' AS id UNION
                SELECT 'Ovo' AS name, 'ovo.png' AS id UNION
                SELECT 'Permata' AS name, 'permata.png' AS id
                ORDER BY name";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function bank_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' - ' || a.coaname) AS coa, a.coaid
                FROM m_coa a
                WHERE a.is_valid = 't' AND a.coatid = 1
                    AND a.coaid NOT IN (SELECT coaid FROM default_coa WHERE default_code IN ('RETAINEDEARNING_ACCT', 'INCOMESUMMARY_ACCT'))
                ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function kas_bank_detail ($bank_id) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM m_bank WHERE bank_id = ?";
        $rs = DB::Execute($sql, array($bank_id));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM m_bank WHERE LOWER(bank_kode) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['bank_id'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_kas_bank () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bank_id = get_var('bank_id', 0);
        $bank_type = get_var('bank_type');
        $kode_bank = get_var('kode_bank');
        $nama_bank = get_var('nama_bank');
        $alamat = get_var('alamat');
        $kota = get_var('kota');
        $kontak = get_var('kontak');
        $no_telp = get_var('no_telp');
        $bank_no_rek = get_var('bank_no_rek');
        $bank_atas_nama = get_var('bank_atas_nama');
        $bank_cabang = get_var('bank_cabang');
        $bank_img = get_var('bank_img');
        $default_coaid = get_var('default_coaid', NULL);
        $ctrl_acc_coaid = get_var('ctrl_acc_coaid', NULL);
        $persen_adm_deb = get_var('persen_adm_deb');
        $persen_adm_cre = get_var('persen_adm_cre');
        $is_cc = get_var('is_cc', 'f');
        $is_transfer = get_var('is_transfer', 'f');
        $is_petty_cash = get_var('is_petty_cash', 'f');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        // Diluar bank_type = 1, hardcode jadi false
        if ($bank_type != 1)
        {
            $is_cc = 'f';
            $is_transfer = 'f';
        }

        $record = array();
        $record['bank_type']        = $bank_type;
        $record['bank_kode']        = $kode_bank;
        $record['bank_nama']        = $nama_bank;
        $record['alamat']           = $alamat;
        $record['kota']             = $kota;
        $record['kontak']           = $kontak;
        $record['no_telp']          = $no_telp;
        $record['bank_no_rek']      = $bank_no_rek;
        $record['bank_atas_nama']   = $bank_atas_nama;
        $record['bank_cabang']      = $bank_cabang;
        $record['bank_img']         = $bank_img;
        $record['default_coaid']    = $default_coaid;
        $record['ctrl_acc_coaid']   = $ctrl_acc_coaid;
        $record['persen_adm_deb']   = $persen_adm_deb;
        $record['persen_adm_cre']   = $persen_adm_cre;
        $record['is_cc']            = $is_cc;
        $record['is_transfer']      = $is_transfer;
        $record['is_petty_cash']    = $is_petty_cash;
        $record['is_aktif']         = $is_aktif;

        $sql = "SELECT * FROM m_bank WHERE bank_id = ?";
        $rs = DB::Execute($sql, array($bank_id));

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