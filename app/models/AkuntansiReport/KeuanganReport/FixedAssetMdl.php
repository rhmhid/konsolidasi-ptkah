<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class FixedAssetMdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list ($data) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $bid = Auth::user()->branch->bid;

        $smonth = $data['smonth'];
        $syear = $data['syear'];
        $facid = $data['facid'];
        $falid = $data['falid'];
        $kode_nama_desc = $data['kode_nama_desc'];

        if ($facid) $addsql .= " AND mb.facid = ".$facid;

        if ($falid) $addsql .= " AND gl.falid = ".$falid;

        $depre_date = $syear.'-'.$smonth.'-01';
        $depre_date = date('Y-m-t', strtotime($depre_date));

        $sql = "SELECT a.faid, a.facid, b.facid, b.nama_kategori, a.facode
                    , a.faname, a.fadate, a.masa_manfaat, a.nilai_perolehan
                    , a.nilai_minimum, c.lokasi_nama, COUNT(d.fatid) AS dpr_count
                    , SUM(d.depre_amount) AS akumulasi
                FROM fixed_asset a
                INNER JOIN fixed_asset_category b ON b.facid = a.facid
                INNER JOIN fixed_asset_lokasi c ON c.falid = a.falid
                INNER JOIN fixed_asset_trans d ON a.faid = d.faid
                WHERE a.bid = ? AND a.fastatus IN (3, 4) AND DATE(a.fadate) <= DATE('$depre_date')
                    AND DATE(d.depre_date) <= DATE('$depre_date') $addsql
                GROUP BY a.faid, a.facid, b.facid, b.nama_kategori, a.facode
                    , a.faname, a.fadate, a.masa_manfaat, a.nilai_perolehan
                    , a.nilai_minimum, c.lokasi_nama
                ORDER BY a.fadate, a.facode";
        $rs = DB::Execute($sql, [$bid]);

        return $rs;
    } /*}}}*/
}
?>