<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class BarangMdl extends DB
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
        $s_kbid = $data['s_kbid'];
        $s_kode_nama_brg = strtolower(trim($data['s_kode_nama_brg']));
        $s_is_aktif = $data['s_is_aktif'];

        if ($s_kbid) $addsql .= " AND a.kbid = ".$s_kbid;

        if ($s_kode_nama_brg) $addsql .= " AND LOWER(a.kode_brg || a.nama_brg) ILIKE '%$s_kode_nama_brg%'";

        if ($s_is_aktif) $addsql .= " AND a.is_aktif = '$s_is_aktif'";

        $sql = "SELECT a.*, b.nama_kategori AS kel_brg, c.nama_satuan,
                    (SELECT z.kode_satuan || '. Isi. ' || z.isikecil FROM konversi_satuan z WHERE z.mbid = a.mbid AND z.is_aktif = 't' ORDER BY z.isikecil DESC, z.urutan LIMIT 1) AS satuan_besar
                FROM m_barang a
                INNER JOIN m_kategori_barang b ON b.kbid = a.kbid
                INNER JOIN m_satuan c ON c.kode_satuan = a.kode_satuan
                WHERE 1 = 1 $addsql
                ORDER BY a.kode_brg";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function barang_detail ($mbid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*, b.nama_kategori, c.nama_satuan
                FROM m_barang a
                INNER JOIN m_kategori_barang b ON b.kbid = a.kbid
                INNER JOIN m_satuan c ON c.kode_satuan = a.kode_satuan
                WHERE a.mbid = ?";
        $rs = DB::Execute($sql, array($mbid));

        return $rs;
    } /*}}}*/

    public static function detail_barang_satuan ($mbid, $kode_satuan) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT a.*, b.nama_satuan
                FROM konversi_satuan a, m_satuan b
                WHERE b.kode_satuan = a.kode_satuan AND a.mbid = ? AND a.kode_satuan != ?
                ORDER BY a.urutan, a.ksid";
        $rs = DB::Execute($sql, array($mbid, $kode_satuan));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        $sql = "SELECT * FROM m_barang WHERE LOWER(kode_brg) = LOWER(?)";
        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields['mbid'] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_barang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $mbid = get_var('mbid', 0);
        $kbid = get_var('kbid');
        $mmid = get_var('mmid', NULL);
        $kode_brg = get_var('kode_brg');
        $nama_brg = get_var('nama_brg');
        $nama_brg_bill = get_var('nama_brg_bill');
        $kode_satuan = get_var('kode_satuan');
        $hna = get_var('hna');
        $persen_hna = get_var('persen_hna');
        $hna_ppn = get_var('hna_ppn');
        $ppn_jual = get_var('ppn_jual');
        $keterangan = get_var('keterangan');
        $is_aktif = get_var('is_aktif', 'f');
        $ksid = get_var('ksid');
        $kode_satuan_add = get_var('kode_satuan_add');
        $isikecil = get_var('isikecil');
        $is_aktif_satuan = get_var('is_aktif_satuan');
        $update_notes = get_var('alasan', 'Perubahan Data');
        $userid = Auth::user()->pid;
	$bid = Auth::user()->branch->bid;
        $arr_ksid = 0;

        DB::BeginTrans();

        $record = array();
        $record['kbid']             = $kbid;
        $record['mmid']             = $mmid;
        $record['nama_brg']         = $nama_brg;
        $record['nama_brg_bill']    = $nama_brg_bill;
        $record['kode_satuan']      = $kode_satuan;
        $record['hna']              = $hna;
        $record['persen_hna']       = $persen_hna;
        $record['hna_ppn']          = $hna_ppn;
        $record['ppn_jual']         = $ppn_jual;
        $record['keterangan']       = $keterangan;
        $record['is_aktif']         = $is_aktif;

        $sql = "SELECT * FROM m_barang WHERE mbid = ?";
        $rs = DB::Execute($sql, [$mbid]);

        if ($rs->EOF)
        {
            $record['kode_brg']     = DB::GetOne("SELECT generate_kode_brg(?, ?)", [$kbid, $bid]);
            $record['is_medis']     = DB::GetOne("SELECT is_medis FROM m_kategori_barang WHERE kbid = ?", [$kbid]);
            $record['create_by']    = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            $newmbid = DB::GetOne("SELECT CURRVAL('m_barang_mbid_seq')");
            $kode_satuan_lama = $kode_satuan;
        }
        else
        {
            $newmbid = $rs->fields['mbid'];
            $kode_satuan_lama = $rs->fields['kode_satuan'];

            $cek_stok = DB::GetOne("SELECT get_item_qty_all(?, ?, ?)", [$newmbid, $kode_satuan_lama, 'NOW()']);

            if ($is_aktif == 'f' && floatval($cek_stok) > 0)
            {
                DB::RollbackTrans();

                return "Barang Masih Ada Stok {$cek_stok} {$kode_satuan_lama}, Tidak Bisa Dinonaktifkan";
            }

            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);

            $sqlu = "INSERT INTO m_barang_history SELECT $userid, NOW(), '$update_notes', * FROM m_barang WHERE mbid = ?";
            $ok = DB::Execute($sqlu, [$newmbid]);

            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            if ($ok) $ok = DB::Execute($sqlu);
        }

        $sql = "SELECT * FROM konversi_satuan WHERE kode_satuan = ? AND mbid = ?";
        $rss = DB::Execute($sql, [$kode_satuan_lama, $newmbid]);

        $record = array();
        $record['mbid']         = $newmbid;
        $record['kode_satuan']  = $kode_satuan;
        $record['isikecil']     = 1;
        $record['urutan']       = 1;
        $record['is_aktif']     = 't';

        if ($rss->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $userid;

            $sqli = DB::InsertSQL($rss, $record);
            if ($ok) $ok = DB::Execute($sqli);

            $newksid = DB::GetOne("SELECT CURRVAL('konversi_satuan_ksid_seq')");
        }
        else
        {
            if ($rss->fields['kode_satuan'] != '' && $rss->fields['kode_satuan'] != $kode_satuan)
            {
                $cek_data = DB::GetOne("SELECT COUNT(a.invdid)
                                        FROM inventory_d a
                                        INNER JOIN m_barang b ON b.mbid = a.mbid
                                        WHERE b.mbid = ? AND a.kode_satuan = ?", [$newmbid, $rss->fields['kode_satuan']]);

                if ($cek_data > 0)
                {
                    DB::RollbackTrans();

                    return 'Kode Satuan Sudah Digunakan, Tidak Bisa Dirubah';
                }
            }

            $sqlu = "INSERT INTO konversi_satuan_history SELECT $userid, NOW(), '$update_notes', * FROM konversi_satuan WHERE mbid = ? AND kode_satuan = ? AND urutan = 1";
            if ($ok) $ok = DB::Execute($sqlu, [$newmbid, $kode_satuan_lama]);

            $newksid = $rss->fields['ksid'];

            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rss, $record);
            if ($ok) $ok = DB::Execute($sqlu);
        }

        $arr_ksid .= ', '.$newksid;
        if (is_array($kode_satuan_add))
        {
            $i = 2;
            foreach ($kode_satuan_add as $key => $value)
            {
                $ksid[$key] = $ksid[$key] ? $ksid[$key] : 0;

                $sql = "SELECT * FROM konversi_satuan WHERE ksid = ?";
                $rss2 = DB::Execute($sql, [$ksid[$key]]);

                $record = array();
                $record['mbid']         = $newmbid;
                $record['kode_satuan']  = $value;
                $record['isikecil']     = $isikecil[$key];
                $record['urutan']       = $i++;
                $record['is_aktif']     = $is_aktif_satuan[$key];

                if ($rss2->EOF)
                {
                    $record['create_by']    = $record['modify_by'] = $userid;

                    $sqli = DB::InsertSQL($rss2, $record);
                    if ($ok) $ok = DB::Execute($sqli);

                    $newksid = DB::GetOne("SELECT CURRVAL('konversi_satuan_ksid_seq')");
                }
                else
                {
                    $cek_stok = DB::GetOne("SELECT get_item_qty_all(?, ?, ?)", [$newmbid, $rss2->fields['kode_satuan'], 'NOW()']);

                    if ($is_aktif_satuan[$key] == 'f' && floatval($cek_stok) > 0)
                    {
                        DB::RollbackTrans();

                        return "Barang Masih Ada Stok {$cek_stok} {$rss2->fields['kode_satuan']}, Tidak Bisa Dinonaktifkan";
                    }

                    if ($rss2->fields['kode_satuan'] != '' && $rss2->fields['kode_satuan'] != $value)
                    {
                        $cek_data = DB::GetOne("SELECT COUNT(a.invdid)
                                                FROM inventory_d a
                                                INNER JOIN m_barang b ON b.mbid = a.mbid
                                                WHERE b.mbid = ? AND a.kode_satuan = ?", [$newmbid, $rss2->fields['kode_satuan']]);

                        if ($cek_data > 0)
                        {
                            DB::RollbackTrans();

                            return 'Kode Satuan Sudah Digunakan, Tidak Bisa Dirubah';
                        }
                    }

                    $sqlu = "INSERT INTO konversi_satuan_history SELECT $userid, NOW(), '$update_notes', * FROM konversi_satuan WHERE ksid = ?";
                    if ($ok) $ok = DB::Execute($sqlu, [$ksid[$key]]);

                    $newksid = $rss2->fields['ksid'];

                    $record['modify_by']    = $userid;
                    $record['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss2, $record);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_ksid .= ', '.$newksid;
            }

            if ($mbid > 0)
            {
                $cek_ksid = DB::GetOne("SELECT string_agg(ksid::VARCHAR, ',') FROM konversi_satuan WHERE ksid NOT IN ($arr_ksid) AND mbid = ?", [$mbid]);

                $cek_data = DB::GetOne("SELECT COUNT(a.invdid)
                                        FROM inventory_d a
                                        INNER JOIN m_barang b ON b.mbid = a.mbid 
                                        INNER JOIN konversi_satuan c ON c.mbid = a.mbid AND c.kode_satuan = a.kode_satuan
                                        WHERE b.mbid = ? AND c.ksid IN ($cek_ksid)", [$newmbid]);

                if ($cek_data > 0)
                {
                    DB::RollbackTrans();

                    return 'Kode Satuan Sudah Digunakan, Tidak Bisa Dirubah';
                }

                if ($cek_ksid != '')
                {
                    $sql = "DELETE FROM konversi_satuan WHERE ksid NOT IN ($arr_ksid) AND mbid = ?";
                    if ($ok) $ok = DB::Execute($sql, [$newmbid]);
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
            $errmsg = "sql error : " . DB::ErrorMsg();

            DB::RollbackTrans();
            return $errmsg;
        }
    } /*}}}*/
}
?>
