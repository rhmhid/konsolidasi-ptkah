<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class DistribusiBarangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $lp = " LIMIT $paging OFFSET ".$offset;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $gid = $data['gid'];
        $reff_gid = $data['reff_gid'];
        $kbid = $data['kbid'];
        $kode_nama = strtolower(trim($data['kode_nama']));
        $kode_trans = strtolower(trim($data['kode_trans']));
        $is_konfirm = $data['is_konfirm'];

        if ($gid) $addsql .= " AND tb.gid = ".$gid;

        if ($reff_gid) $addsql .= " AND tb.reff_gid = ".$reff_gid;

        if ($kbid) $addsql .= " AND mb.kbid = ".$kbid;

        if ($kode_nama) $addsql .= " AND LOWER(mb.kode_brg || mb.nama_brg) ILIKE '%$kode_nama%'";

        if ($kode_trans) $addsql .= " AND tb.transfer_code = '$kode_trans'";

        if ($is_konfirm == 't') $addsql .= " AND kb.tbid NOTNULL";
        elseif ($is_konfirm == 'f') $addsql .= " AND kb.tbid ISNULL";

        $addsql .= getAksesGudang('tb.gid');

        $addsql .= " AND tb.bid = ".$bid;

        $sql = "SELECT tb.tbid, tb.transfer_code, tb.transfer_date, mb.kode_brg, mb.nama_brg
                    , tbd.kode_satuan, tbd.vol, mgf.nama_gudang AS pengirim
                    , mgt.nama_gudang AS penerima, tbd.ket_item, ps.nama_lengkap AS user_input
                    , kb.kbid
                FROM transfer_barang_d tbd
                INNER JOIN transfer_barang tb ON tb.tbid = tbd.tbid
                INNER JOIN m_barang mb ON mb.mbid = tbd.mbid
                INNER JOIN m_gudang mgf ON mgf.gid = tb.gid
                INNER JOIN m_gudang mgt ON mgt.gid = tb.reff_gid
                INNER JOIN person ps ON ps.pid = tb.create_by
                LEFT JOIN konfirmasi_barang kb ON tb.tbid = kb.tbid
                WHERE DATE(tb.transfer_date) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY tb.transfer_date DESC, tb.transfer_code, tbd.tbdid";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function cari_barang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $key = get_var("q");
        $gid = get_var("gid", 0);
        $reff_gid = get_var("reff_gid", 0);

        $sql = "SELECT a.mbid, a.kode_brg, a.nama_brg, a.kode_satuan, a.all_satuan
                    , COALESCE(a.stock_from, 0) AS stock_from
                    , COALESCE(a.stock_to, 0) AS stock_to
                FROM (
                    SELECT a.mbid, a.kode_brg, a.nama_brg, a.kode_satuan
                        , get_allsatuan(a.mbid) AS all_satuan
                        , get_item_qty(a.mbid, a.kode_satuan, ?, DATE(NOW()), FALSE, $bid) AS stock_from
                        , get_item_qty(a.mbid, a.kode_satuan, ?, DATE(NOW()), FALSE, $bid) AS stock_to
                    FROM m_barang a
                    INNER JOIN branch_assign b ON b.base_id = a.mbid AND b.item_type = 4
                    WHERE b.bid = ? AND a.is_aktif = 't' AND LOWER(a.kode_brg || a.nama_brg) ILIKE LOWER('%$key%')
                ) a
                WHERE a.stock_from > 0
                ORDER BY a.kode_brg";
        $rs = DB::Execute($sql, [$gid, $reff_gid, $bid]);

        return $rs;
    } /*}}}*/

    public static function detail_data ($tbid)
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT b.tbid, b.transfer_code, b.transfer_date, c.kode_brg
                    , c.nama_brg, a.kode_satuan, a.vol, d.nama_gudang AS pengirim
                    , e.nama_gudang AS penerima, b.keterangan, f.nama_lengkap AS petugas
                    , a.ket_item
                FROM transfer_barang_d a
                INNER JOIN transfer_barang b ON b.tbid = a.tbid
                INNER JOIN m_barang c ON c.mbid = a.mbid
                INNER JOIN m_gudang d ON d.gid = b.gid
                INNER JOIN m_gudang e ON e.gid = b.reff_gid
                INNER JOIN person f ON f.pid = b.create_by
                WHERE b.tbid = ? AND b.bid = ?
                ORDER BY a.tbdid";
        $rs = DB::Execute($sql, [$tbid, $bid]);

        return $rs;
    }

    public static function save () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $tbid = 0;
        $transfer_date = 'NOW()';
        $gid = get_var('gid');
        $reff_gid = get_var('reff_gid');
        $keterangan = get_var('keterangan');

        $mbid = get_var('mbid');
        $kode_satuan = get_var('kode_satuan');
        $stock = get_var('stock');
        $vol_kirim = get_var('vol_kirim');
        $ket_item = get_var('ket_item');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT * FROM transfer_barang WHERE tbid = ?";
        $rs = DB::Execute($sql, array($tbid));

        $record = array();
        $record['transfer_date']    = $transfer_date;
        $record['gid']              = $gid;
        $record['reff_gid']         = $reff_gid;
        $record['keterangan']       = $keterangan;
        $record['bid']              = $bid;
        $record['create_by']        = $record['modify_by'] = $pid;

        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

        if ($ok) $tbid = DB::GetOne("SELECT CURRVAL('transfer_barang_tbid_seq') AS code");

        if (is_array($mbid))
        {
            foreach ($mbid as $k => $v)
            {
                $sql = "SELECT * FROM transfer_barang_d WHERE tbid = ? AND mbid = ?";
                $rss = DB::Execute($sql, array($tbid, $v));

                $wac = DB::GetOne("SELECT get_item_wac(?, DATE(?), ?) AS wacc", [$v, $transfer_date, $bid]);
                $isi_kecil = DB::GetOne("SELECT get_isikecil(?, ?) AS wacc", [$v, $kode_satuan[$v]]);

                $recordd = array();
                $recordd['tbid']        = $tbid;
                $recordd['mbid']        = $v;
                $recordd['kode_satuan'] = $kode_satuan[$v];
                $recordd['wac']         = $wac * $isi_kecil;
                $recordd['stock']       = $stock[$k];
                $recordd['vol']         = $vol_kirim[$k];
                $recordd['ket_item']    = $ket_item[$k];
                $recordd['bid']         = $bid;
                $recordd['create_by']   = $recordd['modify_by'] = $pid;

                $sqli = DB::InsertSQL($rss, $recordd);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }

        $sql = "UPDATE transfer_barang SET is_posted = 't' WHERE tbid = ?";
        if ($ok) $ok = DB::Execute($sql, array($tbid));

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

    public static function delete_trans ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT tbid FROM konfirmasi_barang WHERE tbid = ? AND bid = ?";
        $cek_konfirmasi = DB::GetOne($sql, [$myid, $bid]);

        if ($cek_konfirmasi) return 'Data Distribusi Sudah Dikonfirmasi Oleh Unit';

        $sql = "UPDATE transfer_barang SET is_posted = 'f' WHERE tbid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM transfer_barang_d WHERE tbid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM transfer_barang WHERE tbid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

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
