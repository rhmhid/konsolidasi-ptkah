<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class KonfirmasiDistribusiMdl extends DB
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

        if ($is_konfirm == 'f')
        {
            $addsql .= getAksesGudang('tb.reff_gid');

            $addsql .= " AND tb.bid = ".$bid;

            $sql = "SELECT 'f' AS is_konfirm, tb.tbid AS trans_id, tb.transfer_code AS trans_code
                        , tb.transfer_date AS trans_date, tb.transfer_code AS transfer_code, mb.kode_brg
                        , mb.nama_brg, tbd.kode_satuan, tbd.vol, mgf.nama_gudang AS pengirim
                        , mgt.nama_gudang AS penerima, tbd.ket_item, ps.nama_lengkap AS user_input
                    FROM transfer_barang_d tbd
                    INNER JOIN transfer_barang tb ON tb.tbid = tbd.tbid
                    INNER JOIN m_barang mb ON mb.mbid = tbd.mbid
                    INNER JOIN m_gudang mgf ON mgf.gid = tb.gid
                    INNER JOIN m_gudang mgt ON mgt.gid = tb.reff_gid
                    INNER JOIN person ps ON ps.pid = tb.create_by
                    LEFT JOIN konfirmasi_barang kb ON tb.tbid = kb.tbid
                    WHERE DATE(tb.transfer_date) BETWEEN DATE('$sdate') AND DATE('$edate')
                        AND kb.kbid ISNULL $addsql
                    ORDER BY tb.transfer_date DESC, tb.transfer_code, tbd.tbdid";
        }
        else
        {
            $addsql .= getAksesGudang('kb.reff_gid');

            $addsql .= " AND kb.bid = ".$bid;

            $sql = "SELECT 't' AS is_konfirm, kb.kbid AS trans_id, kb.konfirm_code AS trans_code
                        , kb.konfirm_date AS trans_date, tb.transfer_code AS transfer_code, mb.kode_brg
                        , mb.nama_brg, kbd.kode_satuan, kbd.vol, mgf.nama_gudang AS pengirim
                        , mgt.nama_gudang AS penerima, kbd.ket_item, ps.nama_lengkap AS user_input
                    FROM konfirmasi_barang_d kbd
                    INNER JOIN konfirmasi_barang kb ON kb.kbid = kbd.kbid
                    INNER JOIN m_barang mb ON mb.mbid = kbd.mbid
                    INNER JOIN m_gudang mgf ON mgf.gid = kb.gid
                    INNER JOIN m_gudang mgt ON mgt.gid = kb.reff_gid
                    INNER JOIN person ps ON ps.pid = kb.create_by
                    INNER JOIN transfer_barang tb ON tb.tbid = kb.tbid
                    WHERE DATE(kb.konfirm_date) BETWEEN DATE('$sdate') AND DATE('$edate')
                        $addsql
                    ORDER BY kb.konfirm_date DESC, kb.konfirm_code, kbd.kbdid";
        }
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_data ($tbid)
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT b.tbid, b.transfer_code, b.transfer_date, a.tbdid, a.mbid
                    , c.kode_brg , c.nama_brg, a.kode_satuan, a.vol, d.nama_gudang AS pengirim
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

        $konfirm_date = 'NOW()';
        $tbid = get_var('tbid', 0);
        $keterangan = get_var('keterangan');

        $tbdid = get_var('tbdid');
        $vol_terima = get_var('vol_terima');
        $ket_item = get_var('ket_item');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $sql = "SELECT * FROM transfer_barang WHERE tbid = ?";
        $rs_db = DB::Execute($sql, array($tbid));

        if ($rs_db->EOF) return 'Debug Sql Mode';

        DB::BeginTrans();

        $sql = "SELECT * FROM konfirmasi_barang WHERE tbid = ?";
        $rs = DB::Execute($sql, array($tbid));

        if (!$rs->EOF) return 'Data Sudah Dikonfirmasi Sebelumnya';

        $record = array();
        $record['konfirm_date'] = $konfirm_date;
        $record['gid']          = $rs_db->fields['gid'];
        $record['reff_gid']     = $rs_db->fields['reff_gid'];
        $record['keterangan']   = $keterangan;
        $record['tbid']         = $tbid;
        $record['bid']          = $bid;
        $record['create_by']    = $record['modify_by'] = $pid;

        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

        if ($ok) $kbid = DB::GetOne("SELECT CURRVAL('konfirmasi_barang_kbid_seq') AS code");

        if (is_array($tbdid))
        {
            foreach ($tbdid as $k => $v)
            {
                $sql = "SELECT * FROM transfer_barang_d WHERE tbdid = ?";
                $rs_det = DB::Execute($sql, [$v]);

                $stock_from = DB::GetOne("SELECT get_item_qty(?, ?, ?, DATE(NOW()), FALSE, ?) AS stock", [$rs_det->fields['mbid'], $rs_det->fields['kode_satuan'], $rs_db->fields['gid'], $bid]);
                $stock_to = DB::GetOne("SELECT get_item_qty(?, ?, ?, DATE(NOW()), FALSE, ?) AS stock", [$rs_det->fields['mbid'], $rs_det->fields['kode_satuan'], $rs_db->fields['reff_gid'], $bid]);

                $sql = "SELECT * FROM konfirmasi_barang_d WHERE 1 = 2";
                $rss = DB::Execute($sql);

                $wac = DB::GetOne("SELECT get_item_wac(?, DATE(?), ?) AS wacc", [$rs_det->fields['mbid'], $konfirm_date, $bid]);
                $isi_kecil = DB::GetOne("SELECT get_isikecil(?, ?) AS wacc", [$rs_det->fields['mbid'], $rs_det->fields['kode_satuan']]);

                $recordd = array();
                $recordd['kbid']        = $kbid;
                $recordd['mbid']        = $rs_det->fields['mbid'];
                $recordd['kode_satuan'] = $rs_det->fields['kode_satuan'];
                $recordd['wac']         = $wac * $isi_kecil;
                $recordd['stock_from']  = $stock_from;
                $recordd['stock_to']    = $stock_to;
                $recordd['vol']         = $vol_terima[$k];
                $recordd['ket_item']    = $ket_item[$k];
                $recordd['bid']         = $bid;
                $recordd['tbdid']       = $v;
                $recordd['create_by']   = $recordd['modify_by'] = $pid;

                $sqli = DB::InsertSQL($rss, $recordd);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }

        $sql = "UPDATE konfirmasi_barang SET is_posted = 't' WHERE kbid = ?";
        if ($ok) $ok = DB::Execute($sql, array($kbid));

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

        $sql = "UPDATE konfirmasi_barang SET is_posted = 'f' WHERE kbid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM konfirmasi_barang_d WHERE kbid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM konfirmasi_barang WHERE kbid = ? AND bid = ?";
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