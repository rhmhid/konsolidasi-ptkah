<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class ProfitCostCenterAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/ProfitCostCenterMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            's_kode_nama'   => get_var('s_kode_nama'),
            's_pcctype'     => get_var('s_pcctype'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = ProfitCostCenterMdl::list($data, true)->RecordCount();
        $rs = ProfitCostCenterMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'pccid'         => $rs->fields['pccid'],
                'pcccode'       => $rs->fields['pcccode'],
                'pccname'       => $rs->fields['pccname'],
                'pcctype_txt'   => $rs->fields['pcctype'] == 1 ? 'Profit Center' : 'Cost Center',
                'is_aktif'      => $rs->fields['is_aktif'],
                'status_txt'    => get_status_aktif($rs->fields['is_aktif']),
                'status_css'    => get_status_aktif($rs->fields['is_aktif'], 'css'),
                'status_icon'   => get_status_aktif($rs->fields['is_aktif'], 'icon'),
            );

            $rs->MoveNext();
        }

        $data = array(
            'draw'              => $data['draw'],
            'recordsTotal'      => $jmlbris,
            'recordsFiltered'   => $jmlbris,
            'data'              => $record
        );

        $this->response($data, REST::HTTP_OK);
    } /*}}}*/

    public function create_get () /*{{{*/
    {
        $pccid = get_var('pccid', 0);
        $is_aktif = 't';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.setup.profit_cost_center.form', compact(
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function edit_get () /*{{{*/
    {
        $pccid = get_var('pccid', 0);
            
        $rs = ProfitCostCenterMdl::profit_cost_detail($pccid);

        $data_center = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $sel_type0 = $data_center->pcctype == '' ? 'selected=""' : '';
        $sel_type1 = $data_center->pcctype == 1 ? 'selected=""' : '';
        $sel_type2 = $data_center->pcctype == 2 ? 'selected=""' : '';
        $chk_aktif = $data_center->is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($data_center->is_aktif);

        return view('akunting.setup.profit_cost_center.form', compact(
            'data_center',
            'sel_type0',
            'sel_type1',
            'sel_type2',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    function cek_kode_post ($kode) /*{{{*/
    {
        $res = ProfitCostCenterMdl::cek_kode($kode);

        $dtJSON = array();
        if ($res == '')
            $dtJSON = array(
                'success'   => true,
                'message'   => '',
                'kode'      => $kode
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $res,
                'kode'      => $kode
            );

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = ProfitCostCenterMdl::save_profit_cost_center();

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Disimpan'
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $msg
            );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/
}
?>