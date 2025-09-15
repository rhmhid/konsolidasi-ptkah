<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PenerimaanBarangMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data, $istot = false, $offset = 0, $paging = PAGE_ROWS) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addsql = "";
        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $gid = $data['gid'];
        $suppid = $data['suppid'];
        $grcode = strtolower(trim($data['grcode']));
        $pocode = strtolower(trim($data['pocode']));
        $kode_nama = strtolower(trim($data['kode_nama']));
        $no_faktur = strtolower(trim($data['no_faktur']));
        $keterangan = strtolower(trim($data['keterangan']));

        if ($gid) $addsql .= " AND gr.gid = ".$gid;

        if ($suppid) $addsql .= " AND gr.suppid = ".$suppid;

        if ($grcode) $addsql .= " AND gr.grcode = '$grcode'";

        if ($pocode) $addsql .= " AND gr.pocode = '$pocode'";

        if ($kode_nama) $addsql .= " AND (mb.kode_brg || mb.nama_brg) = '$kode_nama'";

        if ($no_faktur) $addsql .= " AND LOWER(a.no_faktur) LIKE '%$no_faktur%'";

        if ($keterangan) $addsql .= " AND LOWER(a.keterangan) LIKE '%$keterangan%'";

        $addsql .= " AND gr.bid = ".$bid;

        $sql = "SELECT gr.grid, gr.grdate, gr.grcode, gr.no_faktur, gr.asal_brg, NULL AS pocode
                    , ms.nama_supp, mg.nama_gudang, (mb.kode_brg || ' - ' || mb.nama_brg) AS barang
                    , msa.nama_satuan, grd.vol, grd.harga, b.nama_lengkap AS useri
                FROM good_receipt gr
                JOIN good_receipt_d grd ON gr.grid = grd.grid
                JOIN m_supplier ms ON ms.suppid = gr.suppid
                JOIN m_gudang mg ON mg.gid = gr.gid
                JOIN m_barang mb ON mb.mbid = grd.mbid
                JOIN m_satuan msa ON msa.kode_satuan = grd.kode_satuan
                JOIN person b ON b.pid = gr.create_by
                WHERE DATE(gr.grdate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY gr.grdate DESC, gr.grid DESC, mb.kode_brg";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_data ($grid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT gr.grid, gr.grcode, gr.grdate, gr.no_faktur, gr.asal_brg, gr.is_medis
                    , gr.gid, mg.nama_gudang, gr.suppid, ms.nama_supp, gr.cara_beli, gr.duedate
                    , gr.keterangan, grd.grdid, grd.mbid, mb.kode_brg, mb.nama_brg, grd.kode_satuan
                    , (mb.kode_brg || ' - ' || mb.nama_brg) AS barang, msa.nama_satuan, grd.vol
                    , grd.harga, grd.disc, grd.disc_rp, grd.subtotal, gr.tgl_faktur, gr.bank_id
                    , grd.harga_dasar, get_allsatuan(mb.mbid) AS all_satuan, grd.exp_date, grd.no_batch
                    , gr.diskon_final_persen, gr.diskon_final, gr.subtotal AS subtot, gr.ongkir
                    , gr.materai, gr.ppn_persen, gr.ppn_rp, gr.other_cost, gr.totalall, grd.is_bonus
                    , gr.poid, grd.podid, ap.grid AS sudah_ap
                    -- , po.pocode, pod.harga_beli AS harga_po, pod.jumlah AS qty_po
                    -- , COALESCE(ret.vol_retur, 0) AS vol_retur
                    -- , COALESCE(pod.qty_terima, 0) AS qty_sudah_grn
                FROM good_receipt_d grd
                INNER JOIN good_receipt gr ON gr.grid = grd.grid
                INNER JOIN m_barang mb ON mb.mbid = grd.mbid
                INNER JOIN m_gudang mg ON mg.gid = gr.gid
                INNER JOIN m_supplier ms ON ms.suppid = gr.suppid
                INNER JOIN m_satuan msa ON msa.kode_satuan = grd.kode_satuan
                -- LEFT JOIN purchase_order_d pod ON grd.podid = pod.podid
                -- LEFT JOIN purchase_order po ON gr.poid = po.poid
                LEFT JOIN (SELECT DISTINCT grid FROM ap_supplier_d) ap ON grd.grid = ap.grid
                -- LEFT JOIN (SELECT grdid, SUM(vol) AS vol_retur FROM retur_barang_d GROUP BY grdid) ret ON grd.grdid = ret.grdid
                WHERE gr.grid = ?
                ORDER BY grd.grdid";
        $rs = DB::Execute($sql, [$grid]);

        return $rs;
    } /*}}}*/

    public static function data_barang () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $key = get_var("q");
        $is_medis = get_var("is_medis");

        $sql = "SELECT a.mbid, a.kode_brg, a.nama_brg, a.kode_satuan
                    , get_allsatuan(a.mbid) AS all_satuan, a.hna
                FROM m_barang a
                JOIN m_kategori_barang b ON b.kbid = a.kbid
                INNER JOIN branch_assign c ON c.base_id = a.mbid AND c.item_type = 4
                WHERE c.bid = ? AND a.is_aktif = 't' AND b.is_medis = '$is_medis'
                    AND LOWER(a.kode_brg || a.nama_brg) ILIKE LOWER('%$key%')
                ORDER BY a.nama_brg";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/

    public static function save () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $grid = get_var('grid', 0);
        $grdate = get_var('grdate');
        $gid = get_var('gid', NULL);
        $duedate = get_var('duedate', date('d-m-Y'));
        $no_faktur = get_var('no_faktur');
        $tgl_faktur = get_var('tgl_faktur', date('d-m-Y'));
        $asal_brg = get_var('asal_brg');
        $is_medis = get_var('is_medis');
        $cara_beli = get_var('cara_beli');
        $suppid = get_var('suppid', NULL);
        $keterangan = get_var('keterangan');
        $poid = get_var('poid', NULL);
        $bank_id = get_var('bank_id', NULL);
        // $is_non_pkp = get_var('is_non_pkp', NULL);
        // $persen_non_pkp = get_var('persen_non_pkp', NULL);

        // Footer
        $total_grn = 0;
        $diskon = get_var('diskon', 0);
        $diskon_rp = get_var('diskon_rp', 0);
        $ongkir = get_var('ongkir', 0);
        $materai = get_var('materai', 0);
        $ppn = get_var('ppn', 0);
        $ppn_rp = get_var('ppn_rp', 0);
        $other_cost = get_var('other_cost', 0);
        $totalall = get_var('totalall', 0);

        // Detail
        $grdid = get_var('grdid');
        $mbid = get_var('mbid');
        $kode_satuan = get_var('kode_satuan');
        $is_bonus = get_var('is_bonus');
        $exp_date = get_var('exp_date');
        $no_batch = get_var('no_batch');
        $jml_terima = get_var('jml_terima');
        $harga_grn = get_var('harga_grn');
        $harga_dasar = get_var('harga_dasar');
        $disc = get_var('disc');
        $disc_rp = get_var('disc_rp');
        $subtotal = get_var('subtotal');
        $podid = get_var('podid');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        $record = array();
        $record['grdate']               = $grdate;
        $record['gid']                  = $gid;
        $record['asal_brg']             = $asal_brg;
        $record['tgl_faktur']           = $tgl_faktur;
        $record['no_faktur']            = $no_faktur;
        $record['duedate']              = $duedate;
        $record['is_medis']             = $is_medis;
        $record['poid']                 = $poid;
        $record['suppid']               = $suppid;
        $record['cara_beli']            = $cara_beli;
        $record['keterangan']           = $keterangan;
        $record['bank_id']              = $cara_beli == 1 ? $bank_id : NULL;
        $record['diskon_final_persen']  = $diskon;
        $record['diskon_final']         = $diskon_rp;
        $record['ongkir']               = $ongkir;
        $record['materai']              = $materai;
        $record['ppn_persen']           = $ppn;
        $record['ppn_rp']               = $ppn_rp;
        $record['other_cost']           = $other_cost;
        $record['totalall']             = $totalall;
        // $record['is_non_pkp']           = $is_non_pkp;

        $sql = "SELECT * FROM good_receipt WHERE grid = ?";
        $rs = DB::Execute($sql, [$grid]);

        if ($rs->EOF)
        {
            $record['create_by']    = $record['modify_by'] = $pid;
            $record['bid']          = $bid;

            $sqli = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($sqli);

            if ($ok) $grid = DB::GetOne("SELECT CURRVAL('good_receipt_grid_seq') AS code");
        }
        else
        {
            $grid = $rs->fields['grid'];

            $sql = "SELECT grid FROM ap_supplier_d WHERE grid = ? AND bid = ?";
            $cek_ap = DB::GetOne($sql, [$apsid, $bid]);

            if ($cek_ap != "")
            {
                DB::RollbackTrans();
                return 'Penerimaan Sudah Dilakukan Proses A/P';
            }

            $record['modify_by']    = $pid;
            $record['modify_time']  = 'NOW()';

            $sqlu = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($sqlu);
        }

        if (is_array($grdid))
        {
            $arr_grdid = "";

            foreach ($grdid as $k => $v)
            {
                $sql = "SELECT * FROM good_receipt_d WHERE grid = ? AND grdid = ?";
                $rss = DB::Execute($sql, [$grid, $v]);

                $recordd = array();
                $recordd['mbid']            = $mbid[$k];
                $recordd['kode_satuan']     = $kode_satuan[$k];
                $recordd['is_bonus']        = $is_bonus[$k] ?? 'f';
                $recordd['exp_date']        = $exp_date[$k];
                $recordd['no_batch']        = $no_batch[$k];
                $recordd['vol']             = $jml_terima[$k];
                $recordd['harga']           = $harga_grn[$k];
                $recordd['harga_dasar']     = $harga_dasar[$k];
                $recordd['disc']            = $disc[$k];
                $recordd['disc_rp']         = $disc_rp[$k];
                $recordd['subtotal']        = $subtotal[$k];
                $recordd['podid']           = $podid[$k];
                // $recordd['is_non_pkp']      = $is_non_pkp;
                // $recordd['persen_non_pkp']  = $persen_non_pkp;

                if ($rss->EOF)
                {
                    $recordd['grid']        = $grid;
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;
                    $recordd['bid']         = $bid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $grdid = DB::GetOne("SELECT CURRVAL('good_receipt_d_grdid_seq') AS code");
                }
                else
                {
                    $grdid = $rss->fields['grdid'];

                    $recordd['modify_by']    = $pid;
                    $recordd['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $arr_grdid .= $grdid.",";
                $total_grn += $subtotal[$k];
            }

            $arr_grdid .= "0";

            $sqld = "DELETE FROM good_receipt_d WHERE grid = ? AND grdid NOT IN ($arr_grdid)";
            if ($ok) $ok = DB::Execute($sqld, [$grid]);
        }

        $sql = "UPDATE good_receipt SET subtotal = $total_grn, is_posted = 't' WHERE grid = ?";
        if ($ok) $ok = DB::Execute($sql, [$grid]);

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

        $sql = "SELECT grid FROM ap_supplier_d WHERE grid = ? AND bid = ?";
        $cek_ap = DB::GetOne($sql, [$myid, $bid]);

        if ($cek_ap != "")
        {
            DB::RollbackTrans();
            return 'Penerimaan Sudah Dilakukan Proses A/P';
        }

        $sql = "UPDATE good_receipt SET is_posted = 'f' WHERE grid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM good_receipt_d WHERE grid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM good_receipt WHERE grid = ? AND bid = ?";
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