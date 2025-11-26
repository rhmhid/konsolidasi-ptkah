<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class PemakaianBarangMdl extends DB
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
        $kode_ciu = trim($data['kode_ciu']);

        if ($gid) $addsql .= " AND b.gid = ".$gid;

        if ($reff_gid) $addsql .= " AND b.reff_gid = ".$reff_gid;

        if ($kbid) $addsql .= " AND c.kbid = ".$kbid;

        if ($kode_nama) $addsql .= " AND LOWER(c.kode_brg || c.nama_brg) ILIKE '%$kode_nama%'";

        if ($kode_ciu) $addsql .= " AND b.ciu_code = '$kode_ciu'";

        $addsql .= getAksesGudang('b.gid');

        $addsql .= " AND b.bid = ".$bid;

        $sql = "SELECT b.ciuid, b.ciu_code, b.ciu_date, c.kode_brg, c.nama_brg
                    , a.kode_satuan, a.vol, d.nama_gudang AS pengirim
                    , e.nama_gudang AS penerima, a.ket_item, f.nama_lengkap AS user_input
                FROM cost_item_usage_d a
                INNER JOIN cost_item_usage b ON b.ciuid = a.ciuid
                INNER JOIN m_barang c ON c.mbid = a.mbid
                INNER JOIN m_gudang d ON d.gid = b.gid
                INNER JOIN m_gudang e ON e.gid = b.reff_gid
                INNER JOIN person f ON f.pid = b.create_by
                WHERE DATE(b.ciu_date) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY b.ciu_date DESC, a.ciudid";
        
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

    public static function detail_data ($ciuid)
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT b.ciuid, b.ciu_code, b.ciu_date, c.kode_brg
                    , c.nama_brg, a.kode_satuan, a.vol, d.nama_gudang AS pengirim
                    , e.nama_gudang AS penerima, b.keterangan, f.nama_lengkap AS petugas
                    , a.ket_item,a.wac
                FROM cost_item_usage_d a
                INNER JOIN cost_item_usage b ON b.ciuid = a.ciuid
                INNER JOIN m_barang c ON c.mbid = a.mbid
                INNER JOIN m_gudang d ON d.gid = b.gid
                INNER JOIN m_gudang e ON e.gid = b.reff_gid
                INNER JOIN person f ON f.pid = b.create_by
                WHERE b.ciuid = ? AND b.bid = ?
                ORDER BY a.ciudid";
        $rs = DB::Execute($sql, [$ciuid, $bid]);

        return $rs;
    }

    public static function save () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $ciuid = 0;
        $ciu_date = 'NOW()';
        $gid = get_var('gid', NULL);
        $reff_gid = get_var('reff_gid', NULL);
        $coaid_cogs = get_var('coaid_cogs', NULL);
        $keterangan = get_var('keterangan');
        $to_bid = get_var('to_bid', NULL);

        $mbid = get_var('mbid');
        $kode_satuan = get_var('kode_satuan');
        $stock = get_var('stock_from');
        $vol = get_var('vol_kirim');
        $ket_item = get_var('ket_item');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "SELECT * FROM cost_item_usage WHERE ciuid = ?";
        $rs = DB::Execute($sql, array($ciuid));

        $record = array();
        $record['ciu_date']     = $ciu_date;
        $record['gid']          = $gid;
        $record['reff_gid']     = $reff_gid;
        $record['coaid_cogs']   = $coaid_cogs;
        $record['keterangan']   = $keterangan;
        $record['to_bid']       = $to_bid;
        $record['bid']          = $bid;
        $record['create_by']    = $record['modify_by'] = $pid;

        $sqli = DB::InsertSQL($rs, $record);
        $ok = DB::Execute($sqli);

        if ($ok) $ciuid = DB::GetOne("SELECT CURRVAL('cost_item_usage_ciuid_seq') AS code");

        if (is_array($mbid))
        {
            foreach ($mbid as $k => $v)
            {
                $sql = "SELECT * FROM cost_item_usage_d WHERE ciuid = ? AND mbid = ?";
                $rss = DB::Execute($sql, array($ciuid, $v));

                $wac = DB::GetOne("SELECT get_item_wac(?, DATE(?), ?) AS wacc", [$v, $ciu_date, $bid]);
                $isi_kecil = DB::GetOne("SELECT get_isikecil(?, ?) AS wacc", [$v, $kode_satuan[$v]]);

                $recordd = array();
                $recordd['ciuid']       = $ciuid;
                $recordd['mbid']        = $v;
                $recordd['kode_satuan'] = $kode_satuan[$v];
                $recordd['wac']         = $wac * $isi_kecil;
                $recordd['stock']       = $stock[$k];
                $recordd['vol']         = $vol[$k];
                $recordd['ket_item']    = $ket_item[$k];
                $recordd['create_by']   = $recordd['modify_by'] = $pid;
                $recordd['bid']         = $bid;

                $sqli = DB::InsertSQL($rss, $recordd);
                if ($ok) $ok = DB::Execute($sqli);
            }
        }

        $sql = "UPDATE cost_item_usage SET is_posted = 't' WHERE ciuid = ?";
        if ($ok) $ok = DB::Execute($sql, array($ciuid));

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

    public static function delete_data ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $sql = "UPDATE cost_item_usage SET is_posted = 'f' WHERE ciuid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM cost_item_usage_d WHERE ciuid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM cost_item_usage WHERE ciuid = ? AND bid = ?";
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
