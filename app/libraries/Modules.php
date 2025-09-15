<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modules
{
    private static $db;

    static $laba_periode_lalu, $laba_periode_berjalan, $akun_migrasi;

    static $cashflow_id = '3, 4';

    public function __construct () /*{{{*/
    {
        self::$db = app('adodb')->init();

        self::$laba_periode_lalu = self::$db->Getone("SELECT coaid FROM default_coa WHERE default_code = 'RETAINEDEARNING_ACCT'");

        self::$laba_periode_berjalan = self::$db->Getone("SELECT coaid FROM default_coa WHERE default_code = 'INCOMESUMMARY_ACCT'");

        self::$akun_migrasi = self::$db->Getone("SELECT coaid FROM default_coa WHERE default_code = 'MY_AKUN_MIGRASI'");
    } /*}}}*/

    public static function dataConfigs ($confname = '') /*{{{*/
    {
        $sql = "SELECT data FROM configs WHERE confname = ?";
        $data = self::$db->GetOne($sql, [$confname]);

        return $data;
    } /*}}}*/

    public static function data_agama () /*{{{*/
    {
        $sql = "SELECT 'Islam' AS name, 'islam' AS id, 1 AS urutan UNION ALL
                SELECT 'Kristen' AS name, 'kristen' AS id, 2 AS urutan UNION ALL
                SELECT 'Katholik' AS name, 'katholik' AS id, 3 AS urutan UNION ALL
                SELECT 'Budha' AS name, 'budha' AS id, 4 AS urutan UNION ALL
                SELECT 'Hindu' AS name, 'hindu' AS id, 5 AS urutan UNION ALL
                SELECT 'Kepercayaan' AS name, 'kepercayaan' AS id, 6 AS urutan UNION ALL
                SELECT 'Konghucu' AS name, 'konghucu' AS id, 7 AS urutan UNION ALL
                SELECT 'Lain - Lain' AS name, 'lain2' AS id, 8 AS urutan
                ORDER BY urutan";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_status_pernikahan () /*{{{*/
    {
        $sql = "SELECT 'Tidak Menikah' AS name, 'Tidak Menikah' AS id, 1 AS urutan UNION ALL
                SELECT 'Belum Nikah' AS name, 'Belum Nikah' AS id, 2 AS urutan UNION ALL
                SELECT 'Menikah' AS name, 'Menikah' AS id, 3 AS urutan UNION ALL
                SELECT 'Janda' AS name, 'Janda' AS id, 4 AS urutan UNION ALL
                SELECT 'Duda' AS name, 'Duda' AS id, 5 AS urutan
                ORDER BY urutan";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_jabatan () /*{{{*/
    {
        $addsql = "";

        $sql = "SELECT nama_jabatan, mjid AS id FROM m_jabatan WHERE is_aktif = 't' ORDER BY nama_jabatan";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_divisi () /*{{{*/
    {
        $addsql = "";

        $sql = "SELECT nama_divisi, divid AS id FROM divisi WHERE is_aktif = 't' ORDER BY nama_divisi";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_tenaga () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $addsql = "";

        $sql = "SELECT kode_tenaga, mtid AS id FROM m_tenaga WHERE is_del = 'f' ORDER BY kode_tenaga";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/


    public static function data_cabang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $addsql = "";

        $sql = "SELECT branch_name, bid AS id FROM branch WHERE is_aktif = 't' ORDER BY branch_name";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_provinsi ($is_aktif = '') /*{{{*/
    {
        $addsql = "";

        if ($is_aktif) $addsql .= " AND is_aktif = '$is_aktif'";

        $sql = "SELECT nama_provinsi, nama_provinsi AS id FROM provinsi WHERE 1 = 1 $addsql ORDER BY nama_provinsi";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_kel_brg ($is_aktif = '') /*{{{*/
    {
        $addsql = '';

        if ($is_aktif != '') $addsql .= " AND is_aktif = '$is_aktif'";

        $sql = "SELECT nama_kategori, kbid FROM m_kategori_barang WHERE 1 = 1 $addsql ORDER BY nama_kategori";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_merk ($is_aktif = '') /*{{{*/
    {
        $addsql = '';

        if ($is_aktif != '') $addsql .= " AND is_aktif = '$is_aktif'";

        $sql = "SELECT nama_merk, mmid FROM m_merk WHERE 1 = 1 $addsql ORDER BY nama_merk";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_satuan ($is_aktif = '') /*{{{*/
    {
        $addsql = '';

        if ($is_aktif != '') $addsql .= " AND is_aktif = '$is_aktif'";

        $sql = "SELECT nama_satuan, kode_satuan FROM m_satuan WHERE 1 = 1 $addsql ORDER BY nama_satuan";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_group_otorisasi () /*{{{*/
    {
        $sql = "SELECT description, otogid FROM otorisasi_group WHERE otogid > 0 ORDER BY description";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_bank () /*{{{*/
    {
        $sql = "SELECT bank_nama, bank_id FROM m_bank WHERE is_aktif = 't' ORDER BY LOWER(bank_nama)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_bank_cc () /*{{{*/
    {
        $sql = "SELECT bank_nama, bank_id FROM m_bank WHERE is_aktif = 't' AND coalesce(is_cc,'false') = 't' ORDER BY LOWER(bank_nama)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/
    public static function data_all_bank_cc () /*{{{*/
    {
        $sql = "SELECT * FROM m_bank WHERE is_aktif = 't' AND coalesce(is_cc,'false') = 't' ORDER BY LOWER(bank_nama)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_posted () /*{{{*/
    {
        $sql = "SELECT 'Posted' AS name, 't' AS id UNION
                SELECT 'Not Posted' AS name, 'f' AS id
                ORDER BY id DESC";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_tipe_jurnal () /*{{{*/
    {
        $sql = "SELECT journal_name, jtid FROM journal_type ORDER BY journal_name";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_coa () /*{{{*/
    {
        $sql = "SELECT (coacode || ' - ' || coaname) AS coa, coaid FROM m_coa WHERE is_valid = 't' AND allow_post = 't' ORDER BY coacode";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/


    public static function data_coa_manual_ar () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT (coacode || ' - ' || coaname) AS coa, coaid
                FROM m_coa
                WHERE is_valid = 't' AND allow_post = 't'
                    AND (coatid IN (4, 5) OR coacode IN ('113100', '115001', '115002'))
                ORDER BY coacode";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_coa_addless_ar () /*{{{*/
    {
        $sql = "SELECT (coacode || ' - ' || coaname) AS coa, coaid
                FROM m_coa
                WHERE is_valid = 't' AND allow_post = 't'
                    AND (coatid > 3 OR coaid IN (770, 772, 773, 774, 775, 776, 777))
                ORDER BY coacode";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_cost_center () /*{{{*/
    {
        $sql = "SELECT (pcccode || ' - ' || pccname) AS pcc, pccid FROM profit_cost_center WHERE is_aktif = 't' ORDER BY pcccode";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    // eksekusi array of sql berikut commit / rollback trans, bila ret = '' maka ok
    public static function array_trans_execute ($sqls, $is_debug = false) /*{{{*/
    {
        if ($is_debug) self::$db->debug = true;

        DB::BeginTrans();

        $ret = '';
        $ok = true;

        if (is_array($sqls)) foreach ($sqls as $k => $v)
        {
            $ok = self::$db->Execute($v);

            if (!$ok)
            {
                $ret .= ' SQL: '.$v;
                break;
            }
        }

        if ($is_debug)
        {
            DB::RollbackTrans();
            die("DEBUG");
        }

        if ($ok && $is_debug == false) DB::CommitTrans();
        else DB::RollbackTrans();

        if ($is_debug)
            $ret = "DEBUG is on, transaction will be discarded";

        return $ret;
    } /*}}}*/

    public static function balance_ledger_tb ($year = 0, $month = 0, $from_report = false) /*{{{*/
    {
        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;
        $message_report = '';

        if ($month == 0)
        {
            $smonth = 1;
            $emonth = 15;
        }
        else
        {
            $smonth = $month;
            $emonth = $month;
        }

        $sqlu = array();
        for ($bln = $smonth; $bln <= $emonth; $bln++)
        {
            $mybulan = $bln > 12 ? 12 : $bln;

            // cari paid
            $sql = "SELECT MAX(a.paid)
                    FROM periode_akunting a
                    WHERE '$year-$mybulan-01' BETWEEN a.pbegin AND a.pend";
            $paid = self::$db->GetOne($sql);

            if ($paid == '') return "ERROR: TIDAK ADA PERIODE AKUNTING PADA $year-$bln-01.";

            $sql = "SELECT COALESCE(c.coacode, e.coacode) AS coacode, COALESCE(c.coaname, e.coaname) AS coaname, d.lsid,
                        COALESCE(c.coaid, e.coaid) AS coaid, b.debet AS d, b.credit AS c, COALESCE(d.amount{$bln}_debet, 0) AS amount{$bln}_debet,
                        COALESCE(d.amount{$bln}_credit, 0) AS amount{$bln}_credit
                    FROM (SELECT * FROM ledger_summary WHERE paid = $paid AND bid = $bid) d
                    FULL JOIN m_coa c ON d.coaid = c.coaid
                    FULL OUTER JOIN (
                        SELECT k.bid, j.coaid, SUM(j.debet) AS debet, SUM(j.credit) AS credit
                        FROM general_ledger k, general_ledger_d j
                        WHERE j.glid = k.glid AND DATE_TRUNC('MONTH', k.gldate) = '$year-$mybulan-01'
                            AND k.is_posted = 't' AND k.bid = $bid
                        GROUP BY k.bid, j.coaid
                    ) b ON d.coaid = b.coaid AND d.bid = b.bid
                    FULL JOIN m_coa e ON b.coaid = e.coaid
                    WHERE
                        (
                        (ABS(coalesce(d.amount{$bln}_debet, 0) - (coalesce(b.debet, 0))) > 0.5)
                        OR
                        (ABS(coalesce(d.amount{$bln}_credit, 0) - (coalesce(b.credit, 0))) > 0.5)
                        OR
                        (
                            (CASE WHEN c.default_debet = 't' THEN
                                (d.amount{$bln}_debet - d.amount{$bln}_credit)
                            ELSE
                                (d.amount{$bln}_credit - d.amount{$bln}_debet)
                            END) != COALESCE(d.amount{$bln}, 0)))";
            $rs = self::$db->Execute($sql);

            if ($rs->EOF)
                if (!$from_report && $pid == 1) $message_report .= "$year-$bln ALREADY BALANCED!<br>\r\n";

            while (!$rs->EOF)
            {
                $coaid = intval($rs->fields['coaid']);

                if (!$from_report && $pid == 1)
                    $message_report .= "{$rs->fields['coacode']} {$rs->fields['coaname']} TB={$rs->fields["amount{$bln}_debet"]}::{$rs->fields["amount{$bln}_credit"]} G/L={$rs->fields['d']}::{$rs->fields['c']}<br>\r\n";

                // ngga usah lock dulu
                // $sqlu[] = "SELECT * FROM ledger_summary WHERE lsid = {$rs->fields['lsid']} FOR UPDATE";

                $diff_d = $rs->fields['d'] - $rs->fields["amount{$bln}_debet"];
                $diff_c = $rs->fields['c'] - $rs->fields["amount{$bln}_credit"];

                if ($rs->fields['lsid'] != '')
                    $sqlu[] = "UPDATE ledger_summary
                                SET amount{$bln}_debet = amount{$bln}_debet + ($diff_d),
                                    amount{$bln}_credit = amount{$bln}_credit + ($diff_c),
                                    modify_by = $pid,
                                    modify_time = NOW()
                                WHERE paid = $paid AND bid = $bid AND lsid = {$rs->fields['lsid']}";
                else
                    $sqlu[] = "INSERT INTO ledger_summary (amount{$bln}_debet, amount{$bln}_credit, paid, coaid, bid, create_by, modify_by)
                                VALUES ($diff_d, $diff_c, $paid, $coaid, $bid, $pid, $pid)";

                $rs->MoveNext();
            }
        }

        $ret = self::array_trans_execute($sqlu);

        if (!$from_report && $pid == 1) $ret = $message_report;

        return $ret;
    } /*}}}*/

    public static function get_period_akunting ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $month = $data['month'];
        $year = $data['year'];

        $month_use = $month > 12 ? 12 : $month;

        // Auto Update ledger_summary
        self::balance_ledger_tb($year, $month, true);

        // Cek periode_akunting Previous
        $sql = "SELECT * FROM periode_akunting WHERE DATE('$year-$month_use-1') BETWEEN pbegin AND pend ORDER BY pbegin DESC";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function laba_rugi ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $month = $data['month'];
        $year = $data['year'];
        $paid = $data['paid'];
        $pbegin = $data['pbegin'];
        $pend = $data['pend'];

        $opbal = 'a.openingbal';
        for ($i = 1; $i < $month; $i++)
            $opbal .= ' + a.amount'.$i;

        $sql = "SELECT a.coatid, UPPER(b.coatype) AS coatype, a.coaid, a.coacode, a.coaname, a.default_debet
                    , (a.coacode || ' ' || a.coaname) AS mycoa, COALESCE(a.pnid, 0) AS pnid
                    , (CASE WHEN a.coaid = ".self::$laba_periode_lalu." THEN ll.openingbal
                    WHEN a.coaid = ".self::$laba_periode_berjalan." THEN lb.openingbal
                    ELSE 0 END) AS openingbal
                    , (CASE WHEN a.coaid = ".self::$laba_periode_lalu." THEN ll.closingbal
                    WHEN a.coaid = ".self::$laba_periode_berjalan." THEN lb.closingbal
                    ELSE 0 END) AS closingbal
                FROM m_coa a
                INNER JOIN m_coatype b ON b.coatid = a.coatid
                LEFT JOIN (
                    SELECT ".self::$laba_periode_lalu." AS coaid
                        , SUM(CASE WHEN b.coatid = 5 AND b.default_debet = 't' THEN a.closingbal * -1 ELSE a.closingbal END) AS openingbal
                        , SUM(CASE WHEN b.coatid = 5 AND b.default_debet = 't' THEN a.closingbal * -1 ELSE a.closingbal END) AS closingbal
                    FROM ledger_summary a, m_coa b, periode_akunting c
                    WHERE b.coaid = a.coaid AND c.paid = a.paid AND b.period_reset = 't'
                        AND b.coatid > 3 AND c.pend < DATE('$pend') AND a.bid = $bid
                ) ll ON a.coaid = ll.coaid
                LEFT JOIN (
                    SELECT ".self::$laba_periode_berjalan." AS coaid
                        , SUM(CASE WHEN b.coatid = 5 AND b.default_debet = 't' THEN ($opbal) * -1 ELSE ($opbal) END) AS openingbal
                        , SUM(CASE WHEN b.coatid = 5 AND b.default_debet = 't' THEN ({$opbal} + a.amount{$month}) * -1 ELSE ({$opbal} + a.amount{$month}) END) AS closingbal
                    FROM ledger_summary a, m_coa b, periode_akunting c
                    WHERE b.coaid = a.coaid AND c.paid = a.paid AND b.period_reset = 't'
                        AND b.coatid > 3 AND a.paid = $paid AND a.bid = $bid
                ) lb ON a.coaid = lb.coaid
                WHERE a.coaid IN (".self::$laba_periode_lalu.", ".self::$laba_periode_berjalan.")
                ORDER BY a.coatid, a.coacode";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function pos_na ($name, $value, $jenis_pos = '') /*{{{*/
    {
         //  if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $addsql = "";

        if ($jenis_pos) $addsql .= " AND jenis_pos = '$jenis_pos'";

        $sql = "SELECT kode_pos, nama_pos, pnid, parent_pnid, sum_total FROM pos_neraca WHERE is_aktif = 't' $addsql ORDER BY urutan";
        $rs = self::$db->Execute($sql);

        $cb = '<select name="'.$name.'" id="'.$name.'" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih POS">
                <option></option>';

        while (!$rs->EOF)
        {
            if ($rs->fields['parent_pnid'] == '' || $rs->fields['sum_total'] == 't') $cb .= '<optgroup label="'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'].'">';
            else
            {
                if ($rs->fields['pnid'] == $value)
                    $cb .= '<option value="'.$rs->fields['pnid'].'" selected="selected">'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'];
                else
                    $cb .= '<option value="'.$rs->fields['pnid'].'">'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'];
            }

            $rs->MoveNext();
        }

        $cb .= '</select>';

        return $cb;
    } /*}}}*/

    public static function pos_pl ($name, $value, $jenis_pos = '') /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;
    
        $addsql = "";

        if ($jenis_pos) $addsql .= " AND jenis_pos IN (4,5,6,7,8) --' $jenis_pos'";

        $sql = "SELECT kode_pos, nama_pos, pplid, parent_pplid, sum_total FROM pos_pl WHERE is_aktif = 't' $addsql ORDER BY urutan";
        $rs = self::$db->Execute($sql);

        $cb = '<select name="'.$name.'" id="'.$name.'" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih POS">
                <option></option>';

        while (!$rs->EOF)
        {
            if ($rs->fields['parent_pplid'] == '' || $rs->fields['sum_total'] == 't') $cb .= '<optgroup label="'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'].'">';
            else
            {
                if ($rs->fields['pplid'] == $value)
                    $cb .= '<option value="'.$rs->fields['pplid'].'" selected="selected">'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'];
                else
                    $cb .= '<option value="'.$rs->fields['pplid'].'">'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'];
            }

            $rs->MoveNext();
        }

        $cb .= '</select>';

        return $cb;
    } /*}}}*/

    public static function pos_cf ($name, $value, $jenis_pos = '') /*{{{*/
    {
        $addsql = "";

        if ($jenis_pos) $addsql .= " AND jenis_pos = '$jenis_pos'";

        $sql = "SELECT kode_pos, nama_pos, pcfid, parent_pcfid, sum_total FROM pos_cashflow WHERE is_aktif = 't' $addsql ORDER BY urutan";
        $rs = self::$db->Execute($sql);

        $cb = '<select name="'.$name.'" id="'.$name.'" class="form-select form-select-sm rounded-1 w-100" data-control="select2" data-allow-clear="true" data-placeholder="Pilih POS">
                <option></option>';

        while (!$rs->EOF)
        {
            if ($rs->fields['parent_pcfid'] == '' || $rs->fields['sum_total'] == 't') $cb .= '<optgroup label="'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'].'">';
            else
            {
                if ($rs->fields['pcfid'] == $value)
                    $cb .= '<option value="'.$rs->fields['pcfid'].'" selected="selected">'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'];
                else
                    $cb .= '<option value="'.$rs->fields['pcfid'].'">'.$rs->fields['kode_pos'].' '.$rs->fields['nama_pos'];
            }

            $rs->MoveNext();
        }

        $cb .= '</select>';

        return $cb;
    } /*}}}*/

    public static function countUserAvailableBranch($pid) /*{{{*/
    {
        $sql = "SELECT COUNT(*) AS cnt
                FROM branch_assign
                WHERE item_type = 1 AND base_id = ?";
        $data = self::$db->GetOne($sql, [$pid]);

        return $data;
    } /*}}}*/

    public static function assignBranch ($pid, $bid) /*{{{*/
    {
        $tbl_user = config_item('simpleauth_users_table');

        $sql = "UPDATE {$tbl_user} SET last_login_bid = $bid WHERE pid = ?";
        $ok = self::$db->Execute($sql, [$pid]);

        return $ok;
    } /*}}}*/

    public static function isUserBranchActive ($pid) /*{{{*/
    {
        $tbl_user = config_item('simpleauth_users_table');

        $sql = "SELECT COUNT(*) AS cnt
                FROM {$tbl_user} a
                INNER JOIN branch_assign ba ON ba.bid = a.last_login_bid AND ba.base_id = a.pid
                WHERE ba.item_type = 1 AND a.pid = ?";
        $data = self::$db->GetOne($sql, [$pid]);

        return $data > 0 ? true : false;
    } /*}}}*/

    public static function get_nama_supplier ($suppid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;
 
       	$sql = "SELECT nama_supp FROM m_supplier WHERE suppid = ?";
    	$data = self::$db->GetOne($sql, [$suppid]);

        return $data;
    } /*}}}*/

    public static function availableBranch ($pid) /*{{{*/
    {
        $q = get_var('q');

        $addSql = '';
        $joinSql = '';

        if ($pid != 1)
        {
            $joinSql .= " INNER JOIN branch_assign ba ON ba.bid = b.bid";
            $addSql .= " AND ba.item_type = 1 AND base_id = ".$pid;
        }

        if ($q)
            $addSql .= " AND LOWER(b.branch_name) LIKE '%$q%'";

        $sql = "SELECT b.*
                FROM branch b
                $joinSql
                WHERE 1 = 1 $addSql";

        return self::$db->Execute($sql);
    } /*}}}*/

    public static function GetGudang ($gid = '') /*{{{*/
    {
        if ($gid == '') return;

        $sql = "SELECT * FROM m_gudang WHERE gid = ?";
        $rs = self::$db->Execute($sql, array($gid));

        return $rs->fields;
    } /*}}}*/

    public static function data_gudang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $addsql = getAksesGudang('gid');

        $sql = "SELECT nama_gudang, gid FROM m_gudang WHERE is_aktif = 't' $addsql ORDER BY LOWER(nama_gudang)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_gudang2 () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $addsql = getAksesGudang('gid');

        $sql = "SELECT nama_gudang, gid FROM m_gudang WHERE is_aktif = 't' AND gid > 0 $addsql ORDER BY LOWER(nama_gudang)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_gudang_besar () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $addsql = getAksesGudang('gid');

        $sql = "SELECT nama_gudang, gid FROM m_gudang WHERE is_aktif = 't' AND gid > 0 AND is_gudang_besar = 't' $addsql ORDER BY LOWER(nama_gudang)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    // Khusus Asset = False
    // Khusus Konsinyasi = False
    // Khusus Service = False
    // Khusus Aktif = True
    public static function data_kel_brg2 () /*{{{*/
    {
        $sql = "SELECT nama_kategori, kbid
                FROM m_kategori_barang
                WHERE is_aktif = 't'
                    AND is_fixed_asset = 'f'
                    AND is_konsinyasi = 'f'
                    AND is_service = 'f'
                ORDER BY nama_kategori";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    // Khusus Asset = False
    // Khusus Service = False
    // Khusus Aktif = True
    public static function data_kel_brg3 () /*{{{*/
    {
        $sql = "SELECT nama_kategori, kbid
                FROM m_kategori_barang
                WHERE is_aktif = 't'
                    AND is_fixed_asset = 'f'
                    AND is_service = 'f'
                ORDER BY nama_kategori";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function setup_coa_inv () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (b.coacode || ' - ' || b.coaname) AS coa, b.coaid FROM setup_coa a, m_coa b WHERE b.coaid = a.coaid AND a.is_aktif = 't' AND a.sctype = 2 ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_barang ($is_aktif = '') /*{{{*/
    {
        $bid = Auth::user()->branch->bid;

        $addsql = '';

        if ($is_aktif != '') $addsql .= " AND a.is_aktif = '$is_aktif'";

        $sql = "SELECT (a.kode_brg || ' - ' || a.nama_brg) AS barang, mbid
                FROM m_barang a
                INNER JOIN branch_assign b ON b.base_id = a.mbid AND b.item_type = 4
                WHERE b.bid = ? $addsql
                ORDER BY a.nama_brg";
        $rs = self::$db->Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/

    public static function GetBarang ($mbid = '') /*{{{*/
    {
        if ($mbid == '') return;

        $sql = "SELECT * FROM m_barang WHERE mbid = ?";
        $rs = self::$db->Execute($sql, array($mbid));

        return $rs->fields;
    } /*}}}*/

    public static function data_coa_ciu () /*{{{*/
    {
        $sql = "SELECT (coacode || ' - ' || coaname) AS coa, coaid
                FROM m_coa
                WHERE is_valid = 't' AND allow_post = 't'
                    AND coacode IN (SELECT UNNEST(STRING_TO_ARRAY(data, ';')::VARCHAR[]) FROM configs WHERE confname = 'coacode_ciu')
                ORDER BY coacode";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_konfirmasi () /*{{{*/
    {
        $sql = "SELECT 'Belum Konfirmasi' AS name, 'f' AS id, 1 AS urutan UNION ALL
                SELECT 'Sudah Konfirmasi' AS name, 't' AS id, 2 AS urutan
                ORDER BY urutan";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_supplier () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.nama_supp, a.suppid 
    			FROM m_supplier a 
    			INNER JOIN branch_assign b ON b.base_id = a.suppid AND b.item_type = 3
    			WHERE a.is_aktif = 't' AND b.bid = ?
                ORDER BY LOWER(a.nama_supp)";
        $rs = self::$db->Execute($sql,$bid);

        return $rs;
    } /*}}}*/

    public static function data_all_supplier () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT * FROM m_supplier WHERE is_aktif = 't' ORDER BY LOWER(nama_supp)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_customer () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.nama_customer, a.custid
                FROM m_customer a 
                INNER JOIN branch_assign b ON b.base_id = a.custid AND b.item_type = 2	
		        WHERE a.is_aktif = 't' AND b.bid = ?
                ORDER BY LOWER(nama_customer)";
        $rs = self::$db->Execute($sql,$bid);

        return $rs;
    } /*}}}*/

    public static function data_all_customer () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT a.kode_customer, a.nama_customer, a.custid, c.branch_name 
                FROM m_customer a
                LEFT JOIN branch_assign b ON a.custid = b.base_id AND b.item_type = 2
                LEFT JOIN branch c ON b.bid = c.bid
                WHERE a.is_aktif = 't'
                ORDER BY LOWER(c.branch_name), LOWER(a.nama_customer)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_karyawan () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT nama_lengkap, pid FROM person WHERE is_aktif = 't' ORDER BY LOWER(nama_lengkap)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_doctor () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT nama_lengkap, pid FROM person WHERE is_aktif = 't' AND is_dokter = 't' ORDER BY LOWER(nama_lengkap)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_cara_bayar () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT 'Transfer' AS name, 1 AS id
                UNION SELECT 'Cash' AS name, 2 AS id
                UNION SELECT 'Giro' AS name, 3 AS id
                ORDER BY id";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    function GetCaraBayar ($cara_bayar = '') /*{{{*/
    {
        if ($cara_bayar == '') return;

        $ret = array();
        $ret[1] = 'Transfer';
        $ret[2] = 'Cash';
        $ret[3] = 'Giro';

        return $ret[$cara_bayar];
    } /*}}}*/

    public static function data_kategori_fa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT nama_kategori, facid FROM fixed_asset_category WHERE is_aktif = 't' ORDER BY LOWER(nama_kategori)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_lokasi_fa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT lokasi_nama, falid FROM fixed_asset_lokasi WHERE is_aktif = 't' ORDER BY LOWER(lokasi_nama)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_status_fa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT 'Not Processed' AS name, 1 AS id UNION
                SELECT 'To Be Approved' AS name, 2 AS id UNION
                SELECT 'Approved' AS name, 3 AS id UNION
                SELECT 'Write Off' AS name, 4 AS id
                ORDER BY id";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_write_off () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT 'Penghapusan Asset' AS name, 1 AS id UNION
                SELECT 'Penjualan Asset' AS name, 2 AS id
                ORDER BY id";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_header_fa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) self::$db->debug = true;

        $sql = "SELECT ('[ ' || facode || ' ] ' || faname) AS name, faid FROM fixed_asset WHERE is_header = 't' ORDER BY LOWER(faname)";
        $rs = self::$db->Execute($sql);

        return $rs;
    } /*}}}*/

    public static function GetSupplier ($suppid = '') /*{{{*/
    {
        if ($suppid == '') return;

        $sql = "SELECT * FROM m_supplier WHERE suppid = ?";
        $rs = self::$db->Execute($sql, array($suppid));

        return $rs->fields;
    } /*}}}*/

    public static function GetPerson ($pid = '') /*{{{*/
    {
        if ($pid == '') return;

        $sql = "SELECT * FROM person WHERE pid = ?";
        $rs = self::$db->Execute($sql, array($pid));

        return $rs->fields;
    } /*}}}*/

    public static function GetBank ($bank_id = '') /*{{{*/
    {
        if ($bank_id == '') return;

        $sql = "SELECT * FROM m_bank WHERE bank_id = ?";
        $rs = self::$db->Execute($sql, array($bank_id));

        return $rs->fields;
    } /*}}}*/

    public static function GetCustomer ($custid = '') /*{{{*/
    {
        if ($custid == '') return;

        $sql = "SELECT * FROM m_customer WHERE custid = ?";
        $rs = self::$db->Execute($sql, [$custid]);

        return $rs->fields;
    } /*}}}*/
}
?>