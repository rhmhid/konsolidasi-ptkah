<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class CoaDefaultAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/CoaDefaultMdl');
    } /*}}}*/

    public function index_get ($sctype) /*{{{*/
    {
        $rs_coa = CoaDefaultMdl::setup_coa($sctype);
        $cmb_coa = $rs_coa->GetMenu2('sc_coaid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sc_coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('akunting.setup.master_coa.coa_default.index', compact(
            'sctype',
            'cmb_coa'
        ));
    } /*}}}*/

    public function list_data_get ($sctype) /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sctype'    => $sctype,
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = CoaDefaultMdl::list($data, true)->RecordCount();
        $rs = CoaDefaultMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "scid"          => $rs->fields['scid'],
                "coa"           => $rs->fields['coa'],
                "create_by"     => $rs->fields['create_by'],
                "create_time"   => dbtstamp2stringlong_ina($rs->fields['create_time']),
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

    public function edit_get () /*{{{*/
    {
        $dcid = get_var('dcid', 0);

        $rs = CoaDefaultMdl::default_coa_detail($dcid);

        $data_def = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $rs_coa = CoaDefaultMdl::list_coa();
        $cmb_coa = $rs_coa->GetMenu2('coaid', $data_def->coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih C.O.A"');

        return view('akunting.setup.master_coa.default_coa.edit', compact(
            'data_def',
            'cmb_coa'
        ));
    } /*}}}*/

    public function save_patch ($sctype) /*{{{*/
    {
        $msg = CoaDefaultMdl::save_coa_default($sctype);

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

    public function update_post ($sctype) /*{{{*/
    {
        $msg = CoaDefaultMdl::update_coa_default($sctype);

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