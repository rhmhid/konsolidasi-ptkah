<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class DefaultCoaAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/DefaultCoaMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = DefaultCoaMdl::list($data, true)->RecordCount();
        $rs = DefaultCoaMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'dcid'          => $rs->fields['dcid'],
                'default_desc'  => $rs->fields['default_desc'],
                'coa'           => $rs->fields['coacode'].' - '.$rs->fields['coaname'],
                'default_type'  => $rs->fields['default_type'] == 1 ? 'By System' : 'By Aplikasi',
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

        $rs = DefaultCoaMdl::default_coa_detail($dcid);

        $data_def = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $rs_coa = DefaultCoaMdl::list_coa();
        $cmb_coa = $rs_coa->GetMenu2('coaid', $data_def->coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih C.O.A"');

        return view('akunting.setup.master_coa.default_coa.edit', compact(
            'data_def',
            'cmb_coa'
        ));
    } /*}}}*/

    public function update_patch () /*{{{*/
    {
        $msg = DefaultCoaMdl::update_default_coa();

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Dirubah'
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