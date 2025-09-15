<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MigrasiDataMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function save_reset_data () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $opsi_reset = get_var('opsi_reset');
        $tipe_reset = get_var('tipe_reset');
        $reset_all_exec = $reset_trans_exec = false;

        $sql_trans = array(
            "TRUNCATE TABLE adjusment CASCADE",
            "ALTER SEQUENCE adjusment_aid_seq RESTART WITH 1",

            "TRUNCATE TABLE ap_supplier CASCADE",
            "ALTER SEQUENCE ap_supplier_apsid_seq RESTART WITH 1",

            "TRUNCATE TABLE ap_supplier_d CASCADE",
            "ALTER SEQUENCE ap_supplier_d_apsdid_seq RESTART WITH 1",

            "TRUNCATE TABLE cost_item_usage CASCADE",
            "ALTER SEQUENCE cost_item_usage_ciuid_seq RESTART WITH 1",

            "TRUNCATE TABLE cost_item_usage_d CASCADE",
            "ALTER SEQUENCE cost_item_usage_d_ciudid_seq RESTART WITH 1",

            "TRUNCATE TABLE fixed_asset CASCADE",
            "ALTER SEQUENCE fixed_asset_faid_seq RESTART WITH 1",

            "TRUNCATE TABLE fixed_asset_lokasi_logs CASCADE",
            "ALTER SEQUENCE fixed_asset_lokasi_logs_fallid_seq RESTART WITH 1",

            "TRUNCATE TABLE fixed_asset_revaluate CASCADE",
            "ALTER SEQUENCE fixed_asset_revaluate_farid_seq RESTART WITH 1",

            "TRUNCATE TABLE fixed_asset_trans CASCADE",
            "ALTER SEQUENCE fixed_asset_trans_fatid_seq RESTART WITH 1",

            "TRUNCATE TABLE general_ledger CASCADE",
            "ALTER SEQUENCE general_ledger_glid_seq RESTART WITH 1",

            "TRUNCATE TABLE general_ledger_d CASCADE",
            "ALTER SEQUENCE general_ledger_d_gldid_seq RESTART WITH 1",

            "DELETE FROM general_ledger_backup",

            "DELETE FROM general_ledger_d_backup",

            "TRUNCATE TABLE good_receipt CASCADE",
            "ALTER SEQUENCE good_receipt_grid_seq RESTART WITH 1",

            "TRUNCATE TABLE good_receipt_d CASCADE",
            "ALTER SEQUENCE good_receipt_d_grdid_seq RESTART WITH 1",

            "TRUNCATE TABLE global_setup CASCADE",
            "ALTER SEQUENCE global_setup_gsid_seq RESTART WITH 1",

            "TRUNCATE TABLE inventory CASCADE",
            "ALTER SEQUENCE inventory_invid_seq RESTART WITH 1",

            "TRUNCATE TABLE inventory_d CASCADE",
            "ALTER SEQUENCE inventory_d_invdid_seq RESTART WITH 1",

            "DELETE FROM inventory_backup",

            "DELETE FROM inventory_d_backup",

            "TRUNCATE TABLE jurnal_manual CASCADE",
            "ALTER SEQUENCE jurnal_manual_jmid_seq RESTART WITH 1",

            "TRUNCATE TABLE jurnal_manual_d CASCADE",
            "ALTER SEQUENCE jurnal_manual_d_jmdid_seq RESTART WITH 1",

            "TRUNCATE TABLE konfirmasi_barang CASCADE",
            "ALTER SEQUENCE konfirmasi_barang_kbid_seq RESTART WITH 1",

            "TRUNCATE TABLE konfirmasi_barang_d CASCADE",
            "ALTER SEQUENCE konfirmasi_barang_d_kbdid_seq RESTART WITH 1",

            "TRUNCATE TABLE ledger_summary CASCADE",
            "ALTER SEQUENCE ledger_summary_lsid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ap CASCADE",
            "ALTER SEQUENCE manual_ap_maid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ap_d CASCADE",
            "ALTER SEQUENCE manual_ap_d_madid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ap_payment CASCADE",
            "ALTER SEQUENCE manual_ap_payment_mapid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ap_payment_d CASCADE",
            "ALTER SEQUENCE manual_ap_payment_d_mapdid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ar CASCADE",
            "ALTER SEQUENCE manual_ar_maid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ar_d CASCADE",
            "ALTER SEQUENCE manual_ar_d_madid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ar_payment CASCADE",
            "ALTER SEQUENCE manual_ar_payment_mapid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ar_payment_addless CASCADE",
            "ALTER SEQUENCE manual_ar_payment_addless_mapaid_seq RESTART WITH 1",

            "TRUNCATE TABLE manual_ar_payment_d CASCADE",
            "ALTER SEQUENCE manual_ar_payment_d_mapdid_seq RESTART WITH 1",

            "TRUNCATE TABLE mutasi_saldo CASCADE",
            "ALTER SEQUENCE mutasi_saldo_msid_seq RESTART WITH 1",

            "TRUNCATE TABLE petty_cash CASCADE",
            "ALTER SEQUENCE petty_cash_pcid_seq RESTART WITH 1",

            "TRUNCATE TABLE petty_cash_d CASCADE",
            "ALTER SEQUENCE petty_cash_d_pcdid_seq RESTART WITH 1",

            "TRUNCATE TABLE saldo_awal CASCADE",
            "ALTER SEQUENCE saldo_awal_said_seq RESTART WITH 1",

            "TRUNCATE TABLE transfer_barang CASCADE",
            "ALTER SEQUENCE transfer_barang_tbid_seq RESTART WITH 1",

            "TRUNCATE TABLE transfer_barang_d CASCADE",
            "ALTER SEQUENCE transfer_barang_d_tbdid_seq RESTART WITH 1",
        );

        $sql_all = array(
            "TRUNCATE TABLE app_users_logs CASCADE",
            "ALTER SEQUENCE app_users_logs_aulid_seq RESTART WITH 1",

            "TRUNCATE TABLE app_users_token CASCADE",
            "ALTER SEQUENCE app_users_token_autid_seq RESTART WITH 1",

            "DELETE FROM app_users WHERE pid > 1",
            "ALTER SEQUENCE app_users_asid_seq RESTART WITH 2",

            'TRUNCATE TABLE login_attempts CASCADE',
            "ALTER SEQUENCE login_attempts_id_seq RESTART WITH 1",

            "DELETE FROM person WHERE pid > 1",
            "ALTER SEQUENCE person_pid_seq RESTART WITH 2",

            "TRUNCATE TABLE role_group CASCADE",
            "ALTER SEQUENCE role_group_rgid_seq RESTART WITH 1",
        );

        if ($opsi_reset == 'all')
        {
            $reset_all_exec = true;
            $reset_trans_exec = true;
        }
        elseif ($opsi_reset == 'only_trans')
            $reset_trans_exec = true;

        DB::BeginTrans();

        if ($reset_trans_exec == true)
        {
            foreach ($sql_trans as $k => $sql)
            {
                $sqle .= "- ".$sql."\r\n";

                $ok = DB::Execute($sql);
            }
        }

        if ($reset_all_exec == true)
        {
            foreach ($sql_all as $k => $sql)
            {
                $sqle .= "- ".$sql."\r\n";

                $ok = DB::Execute($sql);
            }
        }

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            return 'Debug Sql Mode';
        }

        if ($tipe_reset == 'echo')
        {
            DB::RollbackTrans();

            return $sqle;
        }

        if ($ok)
        {
            DB::CommitTrans();
            return 'true';
        }
        else
        {
            $errmsg = "Kesalahan dalam insert data : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/

    public static function list_group_akses ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT * FROM role_group WHERE is_aktif = 't' ORDER BY rgid";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function list_user_akses ($status_akses) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $addsql = "";

        if ($status_akses == 1) $addsql .= " AND b.asid ISNULL";
        elseif ($status_akses == 2) $addsql .= " AND b.asid NOTNULL";

        $sql = "SELECT b.asid, a.pid, a.nip, a.nama_lengkap, b.username, b.clue, b.user_group
                FROM person a
                LEFT JOIN app_users b ON a.pid = b.pid
                WHERE a.pid > 1 $addsql
                ORDER BY a.nama_lengkap";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_import_tb () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $trans_date = get_var('trans_date', date('d-m-Y H:i:s'));
        $coaid_migrasi = get_var('coaid');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // resolve the first line Headers
        array_shift($data);

        DB::BeginTrans();

        foreach ($data as $idx => $row)
        {
            // Skip Header
            if ($idx > 7)
            {
                $coaid = DB::GetOne("SELECT coaid FROM m_coa WHERE coacode = ?", [$row['A']]);

                if ($coaid == '')
                {
                    DB::RollbackTrans();
                    return 'Kode C.O.A [ '.$coaid.' ] Tidak Ditemukan !';
                }

                $record = array();
                $record['trans_date']       = $trans_date;
                $record['coaid_migrasi']    = $coaid_migrasi;
                $record['coaid']            = $coaid;
                $record['debet']            = floatval($row['C']);
                $record['credit']           = floatval($row['D']);
                $record['detailnote']       = $row['E'];
                $record['is_posted']        = 't';
                $record['create_by']        = $record['modify_by'] = $pid;
                $record['bid']              = $bid;

                $sql = "SELECT * FROM saldo_awal WHERE 1 = 2";
                $rs = DB::Execute($sql);
                $sqli = DB::InsertSQL($rs, $record);
                $ok = DB::Execute($sqli);
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
            $errmsg = "Kesalahan dalam insert data : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/

    public static function reset_tb () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "DELETE FROM saldo_awal";
        $ok = DB::Execute($sql);

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
            $errmsg = "Kesalahan dalam insert data : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/

    public static function list_data_kategori ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT * FROM m_kategori_barang WHERE is_aktif = 't' ORDER BY nama_kategori";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function list_data_satuan ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $sql = "SELECT * FROM m_satuan WHERE is_aktif = 't' ORDER BY nama_satuan";

        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function save_import_barang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;

	$bid = Auth::user()->branch->bid;

        $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // resolve the first line Headers
        array_shift($data);

        $ok = true;

        $update_notes = "Update Migrasi";

        $data_xls = [];

        foreach ($data as $idx => $row)
        {
            // Skip Header
            if ($idx > 5)
            {
                $data_xls[$row['A']] = array(
                    'kode_brg'          => $row['A'],
                    'kode_kategori'     => $row['B'],
                    'nama_brg'          => $row['C'],
                    'kode_satuan'       => $row['D'],
                    'isi_kecil'         => $row['E'],
                    'kode_satuan_besar' => $row['F'],
                    'harga_beli'        => $row['G'],
                    'ppn_beli'          => $row['H'],
                    'hna_ppn'           => $row['I'],
                    'ppn_jual'          => $row['J'],
                    'keterangan'        => $row['K'],
                );
            }
        }

        DB::BeginTrans();

        foreach ($data_xls as $idx => $rec)
        {
            $rs_kb = DB::Execute('SELECT kbid, is_medis FROM m_kategori_barang WHERE kode_kategori = ?', [$rec['kode_kategori']]);

            if ($rs_kb->EOF)
            {
                DB::RollbackTrans();
                return 'Kategori Barang [ '.$rec['kode_kategori'].' ] Tidak Ditemukan ! sql : '.DB::ErrorMsg();
                break; // Stop looping
            }
            else
            {
                $kbid = $rs_kb->fields['kbid'];
                $is_medis = $rs_kb->fields['is_medis'];
            }

            $kode_brg = $rec['kode_brg'] ? $rec['kode_brg'] : DB::GetOne("SELECT generate_kode_brg(?, ?)", [$kbid, $bid]);

            $kode_satuan_kecil = DB::GetOne("SELECT kode_satuan FROM m_satuan WHERE kode_satuan = ?", [$rec['kode_satuan']]);

            if ($kode_satuan_kecil == '')
            {
                return 'Kode Satuan Kecil [ '.$rec['kode_satuan'].' ] Barang [ '.$rec['nama_brg'].' ] Tidak Ditemukan ! sql : '.DB::ErrorMsg();
                break; // Stop looping
            }

            $record = array();
            $record['kbid']         = $kbid;
            $record['nama_brg']     = $record['nama_brg_bill'] = $rec['nama_brg'];
            $record['kode_satuan']  = $kode_satuan_kecil;
            $record['hna']          = floatval($rec['harga_beli']);
            $record['persen_hna']   = floatval($rec['ppn_beli']);
            $record['hna_ppn']      = floatval($rec['hna_ppn']);
            $record['ppn_jual']     = floatval($rec['ppn_jual']);
            $record['keterangan']   = $rec['keterangan'];
            $record['is_aktif']     = 't';

            $sql = "SELECT * FROM m_barang WHERE kode_brg = ?";
            $rs = DB::Execute($sql, [$kode_brg]);

            if ($rs->EOF)
            {
                $record['kode_brg']     = $kode_brg;
                $record['is_medis']     = $is_medis;
                $record['create_by']    = $record['modify_by'] = $pid;

                $sqli = DB::InsertSQL($rs, $record);
                if ($ok) $ok = DB::Execute($sqli);

                $newmbid = DB::GetOne("SELECT CURRVAL('m_barang_mbid_seq')");
                $kode_satuan_lama = $kode_satuan_kecil;
            }
            else
            {
                $sqlu = "INSERT INTO m_barang_history SELECT $pid, NOW(), '$update_notes', * FROM m_barang WHERE mbid = ?";
                if ($ok) $ok = DB::Execute($sqlu, array($newmbid));

                $newmbid = $rs->fields['mbid'];
                $kode_satuan_lama = $rs->fields['kode_satuan'];

                $record['modify_by']    = $pid;
                $record['modify_time']  = 'NOW()';

                $sqlu = DB::UpdateSQL($rs, $record);
                if ($ok) $ok = DB::Execute($sqlu);
            }

            $sql = "SELECT * FROM konversi_satuan WHERE kode_satuan = ? AND mbid = ?";
            $rs = DB::Execute($sql, [$kode_satuan_lama, $newmbid]);

            $record_sat_kecil = array();
            $record_sat_kecil['mbid']         = $newmbid;
            $record_sat_kecil['kode_satuan']  = $kode_satuan_kecil;
            $record_sat_kecil['isikecil']     = 1;
            $record_sat_kecil['urutan']       = 1;
            $record_sat_kecil['is_aktif']     = 't';

            if ($rs->EOF)
            {
                $record_sat_kecil['create_by']    = $record_sat_kecil['modify_by'] = $pid;

                $sqli = DB::InsertSQL($rs, $record_sat_kecil);
                if ($ok) $ok = DB::Execute($sqli);
            }
            else
            {
                $sqlu = "INSERT INTO konversi_satuan_history SELECT $pid, NOW(), '$update_notes', * FROM konversi_satuan WHERE mbid = ? AND kode_satuan = ? AND urutan = 1";
                if ($ok) $ok = DB::Execute($sqlu, array($newmbid, $kode_satuan_lama));

                $record_sat_kecil['modify_by']    = $pid;
                $record_sat_kecil['modify_time']  = 'NOW()';

                $sqlu = DB::UpdateSQL($rs, $record_sat_kecil);
                if ($ok) $ok = DB::Execute($sqlu);
            }

            if ($rec['kode_satuan_besar'] != '' && $rec['isi_kecil'] != '')
            {
                $kode_satuan_besar = DB::GetOne('SELECT kode_satuan FROM m_satuan WHERE kode_satuan = ?', [$rec['kode_satuan_besar']]);

                if ($kode_satuan_besar == '')
                {
                    DB::RollbackTrans();
                    return 'Kode Satuan Besar [ '.$rec['kode_satuan_besar'].' ] Barang [ '.$rec['nama_brg'].' ] Tidak Ditemukan ! sql : '.DB::ErrorMsg();
                    break; // Stop looping
                }

                if ($rec['isi_kecil'] == '')
                {
                    DB::RollbackTrans();
                    return 'Isi Kecil Barang [ '.$rec['nama_brg'].' ] Kosong ! sql : '.DB::ErrorMsg();
                    break; // Stop looping
                }

                $sql = "SELECT * FROM konversi_satuan WHERE kode_satuan = ? AND mbid = ?";
                $rs = DB::Execute($sql, [$kode_satuan_besar, $newmbid]);

                $record_sat_besar = array();
                $record_sat_besar['mbid']         = $newmbid;
                $record_sat_besar['kode_satuan']  = $kode_satuan_besar;
                $record_sat_besar['isikecil']     = $rec['isi_kecil'];
                $record_sat_besar['urutan']       = 2;
                $record_sat_besar['is_aktif']     = 't';

                if ($rs->EOF)
                {
                    $record_sat_besar['create_by']    = $record_sat_besar['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rs, $record_sat_besar);
                    if ($ok) $ok = DB::Execute($sqli);
                }
                else
                {
                    $sqlu = "INSERT INTO konversi_satuan_history SELECT $pid, NOW(), '$update_notes', * FROM konversi_satuan WHERE mbid = ? AND kode_satuan = ?";
                    if ($ok) $ok = DB::Execute($sqlu, array($newmbid, $kode_satuan_besar));

                    $record_sat_besar['modify_by']    = $pid;
                    $record_sat_besar['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rs, $record_sat_besar);
                    if ($ok) $ok = DB::Execute($sqlu);
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
            $errmsg = "Kesalahan dalam insert data : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/

    public static function save_import_stok () /*{{{*/
    {
//                    if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // resolve the first line Headers
        array_shift($data);

        $ok = true;

        $tgl        = date('Y-m-d H:i', strtotime(get_var('trans_date')));
        $gid        = get_var('gid');
        $coaid_cogs = get_var('coaid');
        $keterangan = get_var('keterangan');


        DB::BeginTrans();


        $sql = "SELECT * FROM inventory WHERE 1=2";
        $rs = DB::Execute($sql);

        $rec['invdate']         = $tgl;
        $rec['jtid']            = 2;
        $rec['reff_id']         = -1;
        $rec['reff_code']       = 'MGRS';
        $rec['gid']             = $gid;
        $rec['deskripsi']       = $keterangan;
        $rec['bid']             = $bid;

        $rec['create_by']       = $rec['modify_by'] = $pid;
        $sqli = DB::InsertSQL($rs, $rec);
        if ($ok) $ok = DB::Execute($sqli);

        $invid = DB::GetOne("SELECT CURRVAL('inventory_invid_seq') AS code");
	foreach ($data as $idx => $row)
        {

            // Skip Header
            if ($idx > 5 && $row['A'] !='')
            {

                $cek_brg = DB::Getone("SELECT COUNT(kode_brg) FROM m_barang WHERE kode_brg = ?", array($row['A']));

                if ($cek_brg == 0)
                {
                    DB::RollbackTrans();
                    return 'Kode Barang [ '.$row['A'].' ] Tidak Ditemukan !';
                }

                    $mbid = DB::Getone("SELECT mbid FROM m_barang WHERE kode_brg = ?", array($row['A']));

                    $record['invid']                    = $invid;
                    $record['invdate']                  = $tgl;
                    $record['invcode']                  = 'MGRS';
                    $record['reff_id']                  = -1;
                    $record['reff_code']                = 'MGRS';
                    $record['jtid']                     = 2;
                    $record['suppid']                   = 'null';
                    $record['gid']                      = $gid;
                    $record['detailnote']               = 'MIGRASI STOK '.$row['B'].' '.$row['C'].' '.$row['D'];
                    $record['coaid']                    = DB::Getone("select b.coaid_inv  from m_barang a inner join m_kategori_barang b on (a.kbid=b.kbid) where a.mbid ='".$mbid."'");
                    $record['mbid']                     = $mbid;
                    $record['kode_satuan']              = $row['D'];
                    $record['vol']                      = $row['C'];
                    $record['isikecil']                 = 1;
                    $record['wac']                      = str_replace(',', '', $row['E']);
                    $record['amount']                   = str_replace(',', '', $row['E']) * $row['C'];
                    $record['bid']                      = $bid;

        
                    $record['create_by']       = $pid;
                    $record['modify_by']       = $pid;


                    $sql = "SELECT * FROM inventory_d WHERE 1 = 2";
                    $rs = DB::Execute($sql);
                    $sqli = DB::InsertSQL($rs, $record);
                    $ok = DB::Execute($sqli);
            }

        }

                $sql = "UPDATE inventory SET is_posted = 't' WHERE invid = ?";
                if ($ok) $ok = DB::Execute($sql, [$invid]);

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            die('di debug');
            return 'Debug Sql Mode';
        }

        if ($ok)
        {
            DB::CommitTrans();
            return 'true';
        }
        else
        {
            $errmsg = "Kesalahan dalam insert data : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/


    public static function save_manual_ap () /*{{{*/
    {
        //  if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $trans_date = get_var('trans_date', date('d-m-Y H:i:s'));
        $coaid_migrasi = get_var('coaid');
        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // resolve the first line Headers
        array_shift($data);

        DB::BeginTrans();

        foreach ($data as $idx => $row)
        {

            // Skip Header
            if ($idx > 8)
            {
                $suppid = DB::GetOne("SELECT suppid FROM m_supplier WHERE kode_supp = ?", [$row['A']]);

                if ($suppid == '')
                {
                    DB::RollbackTrans();
                    return 'Kode Supplier [ '.$coaid.' ] Tidak Ditemukan !';
                }

                $record = array();
                $record['apdate']           = $trans_date;
                $record['duedate']          = $row['E'];
                $record['suppid']           = $suppid;
                $record['faktur_pajak']     = $row['F'];
                $record['no_inv']           = $row['C'];
                $record['tgl_faktur_pajak'] = $row['G'];
                $record['keterangan']       = 'MIGRASI MANUAL AP : '.$row['H'];
                $record['ppn']              = 0;
                $record['ppn_rp']           = 0;
                $record['subtotal']         = $row['D'];
                $record['totalall']         = $row['D'];
                $record['bid']              = $bid;
                $record['create_by']        = $record['modify_by'] = $pid;


                $sql = "SELECT * FROM manual_ap WHERE 1 = 2";
                $rs = DB::Execute($sql);
                $sqli = DB::InsertSQL($rs, $record);
                $ok = DB::Execute($sqli);

                if($ok){
                        $maid = DB::GetOne("SELECT CURRVAL('manual_ap_maid_seq') AS code");

                        $sql = "SELECT * FROM manual_ap_d WHERE maid = ? AND coaid = ?";
                        $rss = DB::Execute($sql, [$maid, $coaid_migrasi]);
            
                        $recordd = array();
                        $recordd['maid']        = $maid;
                        $recordd['coaid']       = $coaid_migrasi;
                        $recordd['detailnote']  = $row['H'];
                        $recordd['amount']      = $row['D'];
                        $recordd['bid']         = $bid;

                        if ($rss->EOF)
                        {
                            $recordd['create_by']   = $recordd['modify_by'] = $pid;

                            $sqli = DB::InsertSQL($rss, $recordd);
                            if ($ok) $ok = DB::Execute($sqli);
                        }
                    }

                $sql = "UPDATE manual_ap SET is_posted = 't' WHERE maid = ?";
                if ($ok) $ok = DB::Execute($sql, [$maid]);

            }

        }

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            die;
            return 'Debug Sql Mode';
        }

        if ($ok)
        {
            DB::CommitTrans();
            return 'true';
        }
        else
        {
            $errmsg = "Kesalahan dalam insert data : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/


}
?>
