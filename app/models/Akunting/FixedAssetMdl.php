<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class FixedAssetMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function cek_kode ($mytype, $kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $id = get_var('id');
        $status = '';

        if ($mytype == 'lokasi')
        {
            $sql = "SELECT * FROM fixed_asset_lokasi WHERE LOWER(lokasi_kode) = LOWER(?)";

            $fkey = 'falid';
        }
        elseif ($mytype == 'kategori')
        {
            $sql = "SELECT * FROM fixed_asset_category WHERE LOWER(kode_kategori) = LOWER(?)";

            $fkey = 'facid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$fkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function list_lokasi ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $kode_nama_lok = strtolower(trim($data['kode_nama_lok']));

        if ($kode_nama_lok) $addsql .= " AND LOWER(a.lokasi_kode || a.lokasi_nama) ILIKE '%$kode_nama_lok%'";

        $sql = "SELECT a.*
                FROM fixed_asset_lokasi a
                WHERE 1 = 1 $addsql
                ORDER BY a.lokasi_nama";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function lokasi_detail ($falid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM fixed_asset_lokasi WHERE falid = ?";
        $rs = DB::Execute($sql, array($falid));

        return $rs;
    } /*}}}*/

    public static function save_lokasi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $falid = get_var('falid', 0);
        $lokasi_kode = get_var('lokasi_kode');
        $lokasi_nama = get_var('lokasi_nama');
        $keterangan = get_var('keterangan');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['lokasi_kode']  = $lokasi_kode;
        $record['lokasi_nama']  = $lokasi_nama;
        $record['keterangan']   = $keterangan;
        $record['is_aktif']     = $is_aktif;

        $sql = "SELECT * FROM fixed_asset_lokasi WHERE falid = ?";
        $rs = DB::Execute($sql, array($falid));

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

    public static function list_kategori ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $kode_nama_kate = strtolower(trim($data['kode_nama_kate']));

        if ($kode_nama_kate) $addsql .= " AND LOWER(a.kode_kategori || a.nama_kategori) ILIKE '%$kode_nama_kate%'";

        $sql = "SELECT a.facid, a.kode_kategori, a.nama_kategori, a.is_aktif,
                    (b.coacode || ' - ' || b.coaname) AS coa_fa,
                    (c.coacode || ' - ' || c.coaname) AS coa_accumulated,
                    (d.coacode || ' - ' || d.coaname) AS coa_depreciation
                FROM fixed_asset_category a
                LEFT JOIN m_coa b ON a.coaid_fa = b.coaid
                LEFT JOIN m_coa c ON a.coaid_accumulated = c.coaid
                LEFT JOIN m_coa d ON a.coaid_depreciation = d.coaid
                WHERE 1 = 1 $addsql
                ORDER BY a.kode_kategori";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function kategori_detail ($facid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM fixed_asset_category WHERE facid = ?";
        $rs = DB::Execute($sql, array($facid));

        return $rs;
    } /*}}}*/

    public static function data_setup_coa ($sctype) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (b.coacode || ' - ' || b.coaname) AS coa, b.coaid FROM setup_coa a, m_coa b WHERE b.coaid = a.coaid AND a.is_aktif = 't' AND a.sctype = ? ORDER BY coa";
        $rs = DB::Execute($sql, array($sctype));

        return $rs;
    } /*}}}*/

    public static function data_coa () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT (a.coacode || ' - ' || a.coaname) AS coa, a.coaid FROM m_coa a WHERE a.allow_post = 't' AND a.is_valid = 't' AND a.coatid = 5 ORDER BY coa";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function data_kategori_barang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT nama_kategori, kbid FROM m_kategori_barang WHERE is_fixed_asset = 't' AND is_aktif = 't' ORDER BY nama_kategori";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_kategori () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $facid = get_var('facid', 0);
        $kode_kategori = get_var('kode_kategori');
        $nama_kategori = get_var('nama_kategori');
        $is_monthly = get_var('is_monthly', 'f');
        $coaid_fa = get_var('coaid_fa', NULL);
        $coaid_accumulated = get_var('coaid_accumulated', NULL);
        $coaid_depreciation = get_var('coaid_depreciation', NULL);
        $kbid = get_var('kbid', NULL);
        $format_kode_fa = get_var('format_kode_fa');
        $length_format_kode_fa = get_var('length_format_kode_fa');
        $is_aktif = get_var('is_aktif', 'f');
        $pid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['kode_kategori']            = $kode_kategori;
        $record['nama_kategori']            = $nama_kategori;
        $record['is_monthly']               = $is_monthly;
        $record['coaid_fa']                 = $coaid_fa;
        $record['coaid_accumulated']        = $coaid_accumulated;
        $record['coaid_depreciation']       = $coaid_depreciation;
        $record['kbid']                     = $kbid;
        $record['format_kode_fa']           = $format_kode_fa;
        $record['length_format_kode_fa']    = $length_format_kode_fa;
        $record['is_aktif']                 = $is_aktif;

        $sql = "SELECT * FROM fixed_asset_category WHERE facid = ?";
        $rs = DB::Execute($sql, array($facid));

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $pid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);
        }
        else
        {
            if ($rs->fields['coaid_fa'] != '' && $rs->fields['coaid_fa'] != $coaid_fa)
            {
                /*$cek_data = DB::GetOne("SELECT COUNT(a.faid)
                                                FROM fixed_asset a
                                                INNER JOIN fixed_asset_category b ON b.facid = a.facid
                                                WHERE b.facid = ? AND a.coaid_fa = ?", array($facid, $rs->fields['coaid_fa']));

                if ($cek_data > 0)
                {
                    $errmsg = 'Tidak Bisa Dirubah, C.O.A Fixed Asset Sudah Digunakan';

                    DB::RollbackTrans();
                    return $errmsg;
                }*/
            }

            if ($rs->fields['coaid_accumulated'] != '' && $rs->fields['coaid_accumulated'] != $coaid_accumulated)
            {
                /*$cek_data = DB::GetOne("SELECT COUNT(a.faid)
                                                FROM fixed_asset a
                                                INNER JOIN fixed_asset_category b ON b.facid = a.facid
                                                WHERE b.facid = ? AND a.coaid_accumulated = ?", array($facid, $rs->fields['coaid_accumulated']));

                if ($cek_data > 0)
                {
                    $errmsg = 'Tidak Bisa Dirubah, C.O.A Depresiasi F/A Asset Sudah Digunakan';

                    DB::RollbackTrans();
                    return $errmsg;
                }*/
            }

            if ($rs->fields['kbid'] != '' && $rs->fields['kbid'] != $kbid)
            {
                /*$cek_data = DB::GetOne("SELECT COUNT(a.faid)
                                                FROM fixed_asset a
                                                INNER JOIN fixed_asset_category b ON b.facid = a.facid
                                                WHERE b.facid = ? AND a.kbid = ?", array($facid, $rs->fields['kbid']));

                if ($cek_data > 0)
                {
                    $errmsg = 'Tidak Bisa Dirubah, Kategori Barang Sudah Digunakan';

                    DB::RollbackTrans();
                    return $errmsg;
                }*/
            }

            $record['modify_by']    = $pid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);
        }

        if (DB::isDebug())
        {
            DB::RollbackTrans();
            die('Debug Sql Mode');
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

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $facid = $data['facid'];
        $falid = $data['falid'];
        $fastatus = $data['fastatus'];
        $kode_nama_desc = strtolower(trim($data['kode_nama_desc']));

        if ($facid) $addsql .= " AND a.facid = ".$facid;

        if ($falid) $addsql .= " AND a.falid = ".$falid;

        if ($fastatus) $addsql .= " AND a.fastatus = ".$fastatus;

        if ($kode_nama_desc) $addsql .= " AND LOWER(a.facode || a.faname || a.fadesc) ILIKE '%$kode_nama_desc%'";

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.*, b.nama_kategori, c.lokasi_nama
                FROM fixed_asset a
                JOIN fixed_asset_category b ON b.facid = a.facid
                LEFT JOIN fixed_asset_lokasi c ON a.falid = c.falid
                WHERE DATE(a.fadate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY a.fadate DESC, a.facode";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_asset ($faid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.*, ps.nama_lengkap AS fastatus_byname
                    , (CASE WHEN a.fastatus = 3 THEN 'APPROVE' END) AS fastatus_text
                    , (mc.coacode || ' - ' || mc.coaname) AS coa_fa
                FROM fixed_asset a
                LEFT JOIN person ps ON a.fastatus_by = ps.pid
                JOIN m_coa mc ON mc.coaid = a.coaid_fa 
                WHERE a.faid = ? AND a.bid = ?";
        $rs = DB::Execute($sql, [$faid, $bid]);

        return $rs;
    } /*}}}*/

    public static function detail_depresiasi_asset ($faid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT a.*
                FROM fixed_asset_trans a
                WHERE a.faid = ? AND a.bid = ?
                ORDER BY a.depre_date, a.fatid";
        $rs = DB::Execute($sql, [$faid, $bid]);

        return $rs;
    } /*}}}*/

    public static function save_asset () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $faid = get_var('faid', 0);
        $facid = get_var('facid', NULL);
        $facode = get_var('facode');
        $faname = get_var('faname');
        $fadesc = get_var('fadesc');
        $nilai_perolehan = get_var('nilai_perolehan', 0);
        $nilai_minimum = get_var('nilai_minimum', 0);
        $fadate = get_var('fadate');
        $umur_thn = get_var('umur_thn', 0);
        $umur_bln = get_var('umur_bln', 0);
        $masa_manfaat = ($umur_thn * 12) + $umur_bln;
        $is_monthly = get_var('is_monthly', 'f');
        $skip_depresiasi = get_var('skip_depresiasi', 'f');
        $falid = get_var('falid', NULL);
        $pccid = get_var('pccid', NULL);
        $is_header = get_var('is_header', 'f');
        $parent_faid = get_var('parent_faid', NULL);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['facid']                = $facid;
        $record['faname']               = $faname;
        $record['fadesc']               = $fadesc;
        $record['nilai_perolehan']      = $nilai_perolehan;
        $record['nilai_minimum']        = $nilai_minimum;
        $record['fadate']               = $fadate;
        $record['masa_manfaat']         = $masa_manfaat;
        $record['is_monthly']           = $is_monthly;
        $record['skip_depresiasi']      = $skip_depresiasi;
        $record['falid']                = $falid;
        $record['pccid']                = $pccid;
        $record['is_header']            = $is_header;
        $record['parent_faid']          = $is_header == 't' ? NULL : $parent_faid;
        $record['fastatus_notes']       = $fastatus_notes;

        $sql = "SELECT * FROM fixed_asset WHERE faid = ?";
        $rs = DB::Execute($sql, [$faid]);

        if ($rs->EOF)
        {
            $sql = "SELECT * FROM fixed_asset_category WHERE facid = ?";
            $rsc = DB::Execute($sql, [$facid]);

            $facode = DB::GetOne("SELECT generate_kode_fa(?, ?, ?, ?)", [$fadate, $rsc->fields['format_kode_fa'], $rsc->fields['length_format_kode_fa'], $rsc->fields['kode_kategori']]);

            $record['facode']               = $facode;
            $record['grdate']               = $fadate;
            $record['fastatus']             = 2;
            $record['coaid_fa']             = $rsc->fields['coaid_fa'];
            $record['coaid_accumulated']    = $rsc->fields['coaid_accumulated'];
            $record['coaid_depreciation']   = $rsc->fields['coaid_depreciation'];
            $record['create_by']            = $record['modify_by'] = $pid;
            $record['bid']                  = $bid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);
        }
        else
        {
            $record['modify_by']    = $pid;
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

    public static function approve_asset () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $faid = get_var('faid', 0);
        $fastatus_notes = get_var('fastatus_notes');

        $pid = Auth::user()->pid;

        DB::BeginTrans();

        $record = array();
        $record['fastatus']         = 3;
        $record['fastatus_notes']   = $fastatus_notes;
        $record['fastatus_time']    = 'NOW()';
        $record['modify_by']        = $record['fastatus_by'] = $pid;

        $sql = "SELECT * FROM fixed_asset WHERE faid = ?";
        $rs = DB::Execute($sql, [$faid]);
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

    public static function proses_depresiasi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $trans_date = get_var('trans_date', date('Y-m-d'));
        $facid = get_var('facid');
        $facode = get_var('facode');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $ok = true;
        $addsql = "";

        $temp = array();
        $temp = explode('-', $trans_date);
        $datenow = date('Y-m-d');
        $dayendofMonth = date('t', mktime(0, 0, 0, ($temp[0]) + 1, 0, $temp[1]));

        // Cek Bulan & Tahun dengan tanggal sekarang
        if (strtotime("{$temp[1]}/{$temp[0]}/1") > strtotime($datenow))
            return "Bulan dan tahun anda pilih melewati tanggal hari ini : ".(date('F, j Y'));
        elseif ($temp[0] == date('n') && $temp[1] == date('Y'))
        {
            if (date('j') != $dayendofMonth)
                return "Belum memasuki masa depresiasi pada ".(date('F, j Y', strtotime("{$temp[0]}/$dayendofMonth/$temp[1]")));
        }

        if ($facid != '') $addsql .= " AND a.facid = ".$facid;

        if ($facode != '') $addsql .= " AND a.facode = '$facode'";

        DB::BeginTrans();

        $sql = "SELECT a.*, COALESCE(b.numbdepre, 0) AS numbdepre
                    , EXTRACT(MONTH FROM a.fadate) AS startmonth
                    , EXTRACT(YEAR FROM a.fadate) AS startyear
                    , EXTRACT(DAY FROM a.fadate) AS startday
                    , TO_CHAR(b.maxdate, 'MM/DD/YYYY') AS maxdate
                FROM fixed_asset a
                LEFT JOIN (
                    SELECT b.faid, COUNT(b.fatid) AS numbdepre
                        , MAX(b.depre_date) AS maxdate
                    FROM fixed_asset_trans b, fixed_asset a
                    WHERE b.faid = a.faid AND DATE(b.depre_date) >= DATE(a.fadate)
                    GROUP BY b.faid
                ) b ON a.faid = b.faid
                WHERE a.fastatus = 3 AND a.skip_depresiasi = 'f' AND COALESCE(b.numbdepre, 0) < a.masa_manfaat
                    AND a.bid = $bid $addsql
                ORDER BY a.facode";
        $rs_fa = DB::Execute($sql);

        if ($rs_fa->EOF) return 'Data Tidak Ditemukan';
        else while (!$rs_fa->EOF)
        {
            $arr_fatid = 0;

            $posting_depre = false;

            $proses_depre = true;

            $depre_amount = ($rs_fa->fields['nilai_perolehan'] - $rs_fa->fields['nilai_minimum']) / $rs_fa->fields['masa_manfaat'];

            if ($rs_fa->fields['maxdate'] == '')
            {
                $dayendofMonthEdepre = date('t', mktime(0, 0, 0, ($rs_fa->fields['startmonth']) + 1, 0, $rs_fa->fields['startyear']));

                $depre_date = date('Y-m-d', strtotime("{$rs_fa->fields['startmonth']}/$dayendofMonthEdepre/{$rs_fa->fields['startyear']}"));
            }
            else $depre_date = date('Y-m-d', strtotime($rs_fa->fields['maxdate']));

            $depre_until = date('Y-m-d', strtotime("{$temp[0]}/$dayendofMonth/{$temp[1]}"));

            if (strtotime($depre_date) > strtotime($depre_until)) $proses_depre = false;

            while ($proses_depre)
            {
                $insert_depre = true;

                if (strtotime($depre_date) <= strtotime($rs_fa->fields['maxdate'])) $insert_depre = false;

                if ($insert_depre)
                {
                    $posting_depre = true;

                    $record = array();
                    $record['faid']             = $rs_fa->fields['faid'];
                    $record['depre_date']       = $depre_until;
                    $record['depre_amount']     = $depre_amount;
                    $record['nilai_perolehan']  = $rs_fa->fields['nilai_perolehan'];
                    $record['create_by']        = $record['modify_by'] = $pid;
                    $record['bid']              = $bid;

                    $sql = "SELECT * FROM fixed_asset_trans WHERE 1 = 2";
                    $rs = DB::Execute($sql);
                    $sqli = DB::InsertSQL($rs, $record);
                    if ($ok) $ok = DB::Execute($sqli);

                    $arr_fatid .= ', '.DB::GetOne("SELECT CURRVAL('fixed_asset_trans_fatid_seq') AS code");
                }

                $depre_date = DB::GetOne("SELECT '$depre_date'::TIMESTAMP + INTERVAL '1 MONTH'");
                $depre_date = date('Y-m-t', strtotime($depre_date));

                if (strtotime($depre_date) > strtotime($depre_until)) $proses_depre = false;
            }

            if ($posting_depre)
            {
                $sqlu = "UPDATE fixed_asset_trans SET is_posted = 't' WHERE faid = ? AND fatid IN ($arr_fatid)";
                if ($ok) $ok = DB::Execute($sqlu, [$rs_fa->fields['faid']]);
            }

            $rs_fa->MoveNext();
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

    public static function ubah_lokasi_histori ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $faid = $data['faid'];

        if ($faid) $addsql .= " AND a.faid = ".$faid;

        $sql = "SELECT a.*, c.lokasi_nama AS lokasi_from, (d.pcccode || ' - ' || d.pccname) AS cost_center_from
                    , e.lokasi_nama AS lokasi_to, (f.pcccode || ' - ' || f.pccname) AS cost_center_to
                FROM fixed_asset_lokasi_logs a
                JOIN fixed_asset b ON b.faid = a.faid
                LEFT JOIN fixed_asset_lokasi c ON a.falid = c.falid
                LEFT JOIN profit_cost_center d ON a.pccid = d.pccid
                LEFT JOIN fixed_asset_lokasi e ON a.falid_new = e.falid
                LEFT JOIN profit_cost_center f ON a.pccid_new = f.pccid
                WHERE a.bid = $bid $addsql
                ORDER BY a.create_time DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function save_ubah_lokasi () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $ok = false;
        $faid = get_var('faid', 0);
        $falid_new = get_var('falid_new', NULL);
        $pccid_new = get_var('pccid_new', NULL);
        $notes = get_var('notes');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT * FROM fixed_asset WHERE faid = ?";
        $rs = DB::Execute($sql, [$faid]);

        if (!$rs->EOF)
        {
            $falid_old = $rs->fields['falid'];
            $pccid_old = $rs->fields['pccid'];

            $record = array();
            $record['falid']        = $falid_new;
            $record['pccid']        = $pccid_new;
            $record['modify_time']  = 'NOW()';
            $record['modify_by']    = $record['fastatus_by'] = $pid;

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);

            $record = array();
            $record['faid']         = $faid;
            $record['falid']        = $falid_old;
            $record['falid_new']    = $falid_new;
            $record['pccid']        = $pccid_old;
            $record['pccid_new']    = $pccid_new;
            $record['notes']        = $notes;
            $record['bid']          = $bid;
            $record['create_by']    = $record['modify_by'] = $pid;

            $sql = "SELECT * FROM fixed_asset_lokasi_logs WHERE 1 = 2";
            $rs = DB::Execute($sql);
            $sqli = DB::InsertSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqli);
        }
        else
        {
            DB::RollbackTrans();
            return 'Data Asset Tidak Ditemukan';
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

    public static function save_revaluate_asset () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $ok = false;
        $faid = get_var('faid', 0);
        $nilai_perolehan = get_var('nilai_perolehan_baru', 0);
        $nilai_minimum = get_var('nilai_minimum_baru', 0);
        $nilai_buku = get_var('nilai_buku', 0);
        $fadate = get_var('fadate');
        $umur_thn = get_var('umur_thn', 0);
        $umur_bln = get_var('umur_bln', 0);
        $masa_manfaat = ($umur_thn * 12) + $umur_bln;
        $pccid = get_var('pccid', NULL);
        $notes = get_var('notes');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT * FROM fixed_asset WHERE faid = ?";
        $rs = DB::Execute($sql, [$faid]);

        if (!$rs->EOF)
        {
            $nilai_perolehan_old = $rs->fields['nilai_perolehan'];
            $nilai_minimum_old = $rs->fields['nilai_minimum'];
            $fadate_old = $rs->fields['fadate'];
            $masa_manfaat_old = $rs->fields['masa_manfaat'];
            $pccid_old = $rs->fields['pccid'];

            $record = array();
            $record['nilai_perolehan']  = $nilai_perolehan;
            $record['nilai_minimum']    = $nilai_minimum;
            $record['fadate']           = $fadate;
            $record['masa_manfaat']     = $masa_manfaat;
            $record['pccid']            = $pccid;
            $record['modify_time']      = 'NOW()';
            $record['modify_by']        = $record['fastatus_by'] = $pid;

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);

            $record = array();
            $record['faid']                 = $faid;
            $record['nilai_perolehan_old']  = $nilai_perolehan_old;
            $record['nilai_perolehan_new']  = $nilai_perolehan;
            $record['nilai_buku']           = $nilai_buku;
            $record['nilai_minimum_old']    = $nilai_minimum_old;
            $record['nilai_minimum_new']    = $nilai_minimum;
            $record['fadate_old']           = $fadate_old;
            $record['fadate_new']           = $fadate;
            $record['masa_manfaat_old']     = $masa_manfaat_old;
            $record['masa_manfaat_new']     = $masa_manfaat;
            $record['pccid_old']            = $pccid_old;
            $record['pccid_new']            = $pccid;
            $record['notes']                = $notes;
            $record['bid']                  = $bid;
            $record['create_by']            = $record['modify_by'] = $pid;

            $sql = "SELECT * FROM fixed_asset_revaluate WHERE 1 = 2";
            $rs = DB::Execute($sql);
            $sqli = DB::InsertSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqli);
        }
        else
        {
            DB::RollbackTrans();
            return 'Data Asset Tidak Ditemukan';
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

    public static function data_coa_write_off () /*{{{*/
    {
        $sql = "SELECT (coacode || ' - ' || coaname) AS coa, coaid
                FROM m_coa
                WHERE is_valid = 't' AND allow_post = 't' AND coatid = 5
                ORDER BY coacode";
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function save_write_off_asset () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $ok = false;
        $faid = get_var('faid', 0);
        $wo_date = get_var('wo_date');
        $wo_status = get_var('wo_status');
        $coa_write_off = get_var('coa_write_off', NULL);
        $notes = get_var('notes');
        $nilai_perolehan = get_var('nilai_perolehan', 0);
        $nilai_buku = get_var('nilai_buku', 0);
        $bank_id = get_var('bank_id', NULL);
        $nilai_jual = get_var('nilai_jual', 0);
        $ppn = get_var('ppn', 0);
        $ppn_rp = get_var('ppn_rp', 0);
        $total_jual = get_var('total_jual', 0);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT * FROM fixed_asset WHERE faid = ?";
        $rs = DB::Execute($sql, [$faid]);

        if (!$rs->EOF)
        {
            $fadesc = $rs->fields['fadesc'];

            $record = array();
            $record['wo_time']              = $wo_date;
            $record['wo_status']            = $wo_status;
            $record['wo_coaid']             = $coa_write_off;
            $record['wo_notes']             = $notes;
            $record['wo_nilai_perolehan']   = $nilai_perolehan;
            $record['wo_nilai_buku']        = $nilai_buku;

            if ($wo_status == 2)
            {
                $record['wo_bank_id']       = $bank_id;
                $record['wo_nilai_jual']    = $nilai_jual;
                $record['wo_ppn']           = $ppn;
                $record['wo_ppn_rp']        = $ppn_rp;
                $record['wo_total_jual']    = $total_jual;

                $txtadd = "(Dijual senilai $nilai_jual)";
            }
            else
                $txtadd = "(Dihapus)";

            $oleh = DB::GetOne("SELECT nama_lengkap FROM person WHERE pid = ?", [$pid]);

            $record['fastatus']             = 4;
            $record['fadesc']               = "( WRITE OFF TGL $wo_date $txtadd oleh $oleh : $notes ) ".$fadesc;
            $record['wo_posted']            = true;
            $record['modify_time']          = 'NOW()';
            $record['modify_by']            = $record['wo_by'] = $pid;

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);
        }
        else
        {
            DB::RollbackTrans();
            return 'Data Asset Tidak Ditemukan';
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