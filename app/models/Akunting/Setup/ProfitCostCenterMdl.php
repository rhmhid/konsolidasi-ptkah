<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class ProfitCostCenterMdl extends DB
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
        $s_kode_nama = strtolower(trim($data['s_kode_nama']));
        $s_pcctype = $data['s_pcctype'];

        if ($s_kode_nama) $addsql .= " AND LOWER(pccname || pcccode) ILIKE ('%$s_kode_nama%')";

        if ($s_pcctype) $addsql .= " AND pcctype = ".$s_pcctype;

        $sql = "SELECT * FROM profit_cost_center WHERE 1 = 1 $addsql ORDER BY pcccode";
        
        if ($istot == false) $sqlx = $sql.$lp;
        else $sqlx = $sql;

        $rs = DB::Execute($sqlx);

        return $rs;
    } /*}}}*/

    public static function profit_cost_detail ($pccid) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT * FROM profit_cost_center WHERE pccid = ?";
        $rs = DB::Execute($sql, array($pccid));

        return $rs;
    } /*}}}*/

    public static function cek_kode ($kode) /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $jenis = get_var('jenis');
        $id = get_var('id');
        $status = '';

        if ($jenis == 'pc')
        {
            $sql = "SELECT * FROM profit_cost_center WHERE LOWER(pcccode) = LOWER(?)";

            $pkey = 'pccid';
        }

        $rs = DB::Execute($sql, array($kode));

        if (!$rs->EOF && $rs->fields[$pkey] != $id) $status = 'Data Double';

        return $status;
    } /*}}}*/

    public static function save_profit_cost_center () /*{{{*/
    {
        // if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $pccid = get_var('pccid', 0);
        $pcccode = get_var('pcccode');
        $pccname = get_var('pccname');
        $pcctype = get_var('pcctype');
        $is_aktif = get_var('is_aktif', 'f');
        $userid = Auth::user()->pid;

        DB::BeginTrans();

        $sql = "SELECT * FROM profit_cost_center WHERE pccid = ?";
        $rs = DB::Execute($sql, array($pccid));

        $record = array();
        $record['pcccode']  = $pcccode;
        $record['pccname']  = $pccname;
        $record['pcctype']  = $pcctype;
        $record['is_aktif'] = $is_aktif;

        if ($rs->EOF)
        {
            $record['create_by'] = $record['modify_by'] = $userid;

            $newsql = DB::InsertSQL($rs, $record);
            $ok = DB::Execute($newsql);
        }
        else
        {
            $record['modify_by']    = $userid;
            $record['modify_time']  = 'NOW()';

            $newsql = DB::UpdateSQL($rs, $record);
            $ok = DB::Execute($newsql);
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