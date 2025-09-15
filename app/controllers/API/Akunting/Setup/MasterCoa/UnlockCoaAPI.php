<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class UnlockCoaAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/UnlockCoaMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = UnlockCoaMdl::list($data, true)->RecordCount();
        $rs = UnlockCoaMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "coagid"        => encrypt($rs->fields['coagid']),
                'coa_group'     => $rs->fields['coa_group'],
                'start_period'  => date('d M Y', strtotime($rs->fields['start_period'])),
                'end_period'    => date('d M Y', strtotime($rs->fields['end_period'])),
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
        $coagid = get_var('coagid', 0, 't');

        $rs = UnlockCoaMdl::group_coa_detail($coagid);

        $data_group = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $data_group->start_period = $data_group->start_period == '' ? '' : date('d-m-Y', strtotime($data_group->start_period));

        $data_group->end_period = $data_group->end_period == '' ? '' : date('d-m-Y', strtotime($data_group->end_period));

        return view('akunting.setup.master_coa.unlock_coa.edit', compact('data_group'));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = UnlockCoaMdl::save_unlock_coa();

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