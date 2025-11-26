<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class InvoicePembelianMdl extends DB
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
        $suppid = $data['suppid'];
        $no_inv = strtolower(trim($data['no_inv']));
        $no_faktur_pajak = strtolower(trim($data['no_faktur_pajak']));
        $apcode = strtolower(trim($data['apcode']));

        if ($suppid) $addsql .= " AND a.suppid = ".$suppid;

        if ($no_inv) $addsql .= " AND LOWER(a.no_invoice) = '$no_inv'";

        if ($no_faktur_pajak) $addsql .= " AND LOWER(a.no_faktur_pajak) = '$no_faktur_pajak'";

        if ($apcode) $addsql .= " AND LOWER(a.apcode) = '$apcode'";

        $addsql .= " AND a.bid = ".$bid;

        $sql = "SELECT a.apsid, a.apdate, a.apcode, a.no_invoice, b.nama_supp
                    , a.amount, ps.nama_lengkap AS useri, gl.glid, a.suppid
                FROM ap_supplier a
                INNER JOIN m_supplier b ON b.suppid = a.suppid
                LEFT JOIN person ps ON a.create_by = ps.pid
                LEFT JOIN general_ledger gl ON a.apsid = gl.reff_id AND gl.jtid = 20
                WHERE DATE(a.apdate) BETWEEN DATE('$sdate') AND DATE('$edate')
                    $addsql
                ORDER BY a.apdate DESC";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function detail_trans ($apsid) /*}}}*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $sql = "SELECT ap.apsid, apd.apsdid, ap.apdate, ap.apcode, ap.suppid, gl.glid
                    , format_glcode(gl.gldate, jt.doc_code, gl.gldoc, gl.glid) AS doc_no
                    , ms.nama_supp, ap.no_invoice, ap.duedate, ap.keterangan, ap.no_faktur_pajak
                    , ap.tgl_faktur_pajak, apd.nominal, ps.nama_lengkap AS petugas
                    , ap.subtotal, ap.ppn, ap.ppn_rp, ap.amount, apd.grid, gr.grcode, '' AS pocode
                    , ap.diskon, ap.retur_id, ap.retur_amount, ap.dp_id, ap.dp_amount
                    , ap.materai, ap.ongkir, ap.pembulatan, ap.other_cost, gr.poid
                    , ap.is_kwitansi, ap.is_faktur_pajak, ap.is_surat_jalan, ap.is_po
                    , ap.is_terima_barang, ap.is_nota_retur, ap.is_berita_acara
                FROM ap_supplier ap
                INNER JOIN ap_supplier_d apd ON ap.apsid = apd.apsid
                INNER JOIN m_supplier ms ON ms.suppid = ap.suppid
                INNER JOIN person ps ON ps.pid = ap.create_by
                INNER JOIN good_receipt gr ON gr.grid = apd.grid
                LEFT JOIN general_ledger gl ON ap.apsid = gl.reff_id AND gl.jtid = 20
                LEFT JOIN journal_type jt ON gl.jtid = jt.jtid
                WHERE ap.apsid = ? AND ap.bid = ?
                ORDER BY apd.apsdid";
        $rs = DB::Execute($sql, [$apsid, $bid]);

        return $rs;
    } /*}}}*/

    public static function list_penerimaan () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $addSql = "";
        $suppid = get_var('suppid');
        $no_faktur = strtolower(trim(get_var('no_faktur')));
        $pocode = get_var('pocode');
        $grcode = get_var('grcode');

        if ($no_faktur) $addSql .= " AND LOWER(a.no_faktur) ILIKE LOWER('$no_faktur')";

        // if ($pocode) $addSql .= " AND a.pocode = '$pocode'";

        if ($grcode) $addSql .= " AND a.grcode = '$grcode'";

        $sql = "SELECT a.grid, a.poid
                    -- , b.pocode
                    , '' AS pocode, a.subtotal, a.totalall AS nominal, a.tgl_faktur
                    , a.no_faktur
                    , a.grcode, a.grdate, a.no_faktur, a.keterangan
                    , a.diskon_final AS diskon, a.ongkir, a.materai, a.ppn_persen, a.ppn_rp, a.other_cost
                FROM good_receipt a
                -- LEFT JOIN purchase_order b ON a.poid = b.poid
                LEFT JOIN ap_supplier_d c ON a.grid = c.grid
                WHERE a.cara_beli = 2 AND c.apsid ISNULL AND a.suppid = ? AND a.bid = ?
                    $addSql
                ORDER BY a.grdate";
        $rs = DB::Execute($sql, [$suppid, $bid]);

        return $rs;
    } /*}}}*/

    public static function save_trans () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        // Header
        $apsid = get_var('apsid', 0);
        $apdate = get_var('apdate');
        $duedate = get_var('duedate');
        $suppid = get_var('suppid', NULL);
        $keterangan = get_var('keterangan');
        $is_kwitansi = get_var('is_kwitansi', 'f');
        $is_faktur_pajak = get_var('is_faktur_pajak', 'f');
        $is_surat_jalan = get_var('is_surat_jalan', 'f');
        $is_po = get_var('is_po', 'f');
        $is_terima_barang = get_var('is_terima_barang', 'f');
        $is_nota_retur = get_var('is_nota_retur', 'f');
        $is_berita_acara = get_var('is_berita_acara', 'f');

        // Detail
        $apsdid = get_var('apsdid');
        $grid = get_var('grid');
        $tgl_faktur = get_var('tgl_faktur');
        $no_faktur = get_var('no_faktur');
        $no_inv = get_var('no_inv');
        $total_grn = get_var('total_grn');
        $diskon = get_var('diskon');
        $retur_id = get_var('retur_id');
        $retur_amount = get_var('retur_amount');
        $dp_id = get_var('dp_id');
        $dp_amount = get_var('dp_amount');
        $ongkir = get_var('ongkir');
        $materai = get_var('materai');
        $ppn_persen = get_var('ppn_persen');
        $ppn_rp = get_var('ppn_rp');
        $other_cost = get_var('other_cost');
        $pembulatan = get_var('pembulatan');
        $total_ap = get_var('total_ap');

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        DB::BeginTrans();

        if (is_array($grid))
        {
            foreach ($grid as $id)
            {
                $record = array();
                $record['apdate']           = $apdate;
                $record['duedate']          = $duedate;
                $record['suppid']           = $suppid;
                $record['keterangan']       = $keterangan;
                $record['is_kwitansi']      = $is_kwitansi;
                $record['is_faktur_pajak']  = $is_faktur_pajak;
                $record['is_surat_jalan']   = $is_surat_jalan;
                $record['is_po']            = $is_po;
                $record['is_terima_barang'] = $is_terima_barang;
                $record['is_nota_retur']    = $is_nota_retur;
                $record['is_berita_acara']  = $is_berita_acara;
                $record['tgl_faktur_pajak'] = $tgl_faktur[$id];
                $record['no_faktur_pajak']  = $no_faktur[$id];
                $record['no_invoice']       = $no_inv[$id];
                $record['subtotal']         = $total_grn[$id];
                $record['diskon']           = $diskon[$id];
                $record['retur_id']         = $retur_id[$id];
                $record['retur_amount']     = $retur_amount[$id];
                $record['dp_id']            = $dp_id[$id];
                $record['dp_amount']        = $dp_amount[$id];
                $record['ongkir']           = $ongkir[$id];
                $record['materai']          = $materai[$id];
                $record['ppn']              = $ppn_persen[$id];
                $record['ppn_rp']           = $ppn_rp[$id];
                $record['other_cost']       = $other_cost[$id];
                $record['pembulatan']       = $pembulatan[$id];
                $record['amount']           = $total_ap[$id];

                $sql = "SELECT * FROM ap_supplier WHERE apsid = ?";
                $rs = DB::Execute($sql, [$apsid]);

                if ($rs->EOF)
                {
                    $record['bid']          = $bid;
                    $record['create_by']    = $record['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rs, $record);
                    $ok = DB::Execute($sqli);

                    $apsid = DB::GetOne("SELECT CURRVAL('ap_supplier_apsid_seq') AS code");
                }
                else
                {
                    $apsid = $rs->fields['apsid'];

                    $cek_data = floatval(DB::GetOne("SELECT COUNT(*) FROM ap_payment_d WHERE apsid = ? AND bid = ?", [$apsid, $bid]));

                    if ($cek_data > 0)
                    {
                        DB::RollbackTrans();

                        return 'Invoice Sudah Ada Pembayaran';
                    }

                    $sqlu = "UPDATE ap_supplier SET is_posted = 'f' WHERE apsid = ?";
                    $ok = DB::Execute($sqlu, [$apsid]);

                    $record['modify_by']    = $pid;
                    $record['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rs, $record);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $sql = "SELECT * FROM ap_supplier_d WHERE apsid = ? AND grid = ?";
                $rss = DB::Execute($sql, [$apsid, $id]);

                $recordd = array();
                $recordd['apsid']   = $apsid;
                $recordd['grid']    = $id;
                $recordd['nominal'] = $total_grn[$id];

                if ($rss->EOF)
                {
                    $recordd['bid']         = $bid;
                    $recordd['create_by']   = $recordd['modify_by'] = $pid;

                    $sqli = DB::InsertSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqli);

                    $apsdid = DB::GetOne("SELECT CURRVAL('ap_supplier_d_apsdid_seq') AS code");
                }
                else
                {
                    $apsdid = $rss->fields['apsdid'];

                    $recordd['modify_by']    = $pid;
                    $recordd['modify_time']  = 'NOW()';

                    $sqlu = DB::UpdateSQL($rss, $recordd);
                    if ($ok) $ok = DB::Execute($sqlu);
                }

                $sqld = "DELETE FROM ap_supplier_d WHERE apsid = ? AND apsdid NOT IN ($apsdid)";
                if ($ok) $ok = DB::Execute($sqld, [$apsid]);

                $sql = "UPDATE ap_supplier SET is_posted = 't' WHERE apsid = ?";
                if ($ok) $ok = DB::Execute($sql, [$apsid]);

                $apsid = 0;
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

    public static function delete_trans ($myid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pid = Auth::user()->pid;
        $bid = Auth::user()->branch->bid;

        $cek_data = floatval(DB::GetOne("SELECT COUNT(*) FROM ap_payment_d WHERE apsid = ? AND bid = ?", [$myid, $bid]));

        if ($cek_data > 0) return 'Invoice Sudah Ada Pembayaran';

        DB::BeginTrans();

        $sql = "UPDATE ap_supplier SET is_posted = 'f' WHERE apsid = ? AND bid = ?";
        $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM ap_supplier_d WHERE apsid = ? AND bid = ?";
        if ($ok) $ok = DB::Execute($sql, [$myid, $bid]);

        $sql = "DELETE FROM ap_supplier WHERE apsid = ? AND bid = ?";
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
