<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PeriodeAkuntingAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/PeriodeAkuntingMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = PeriodeAkuntingMdl::list($data, true)->RecordCount();
        $rs = PeriodeAkuntingMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "paid"      => encrypt($rs->fields['paid']),
                'pbegin'    => date('M Y', strtotime($rs->fields['pbegin'])),
                'pend'      => date('M Y', strtotime($rs->fields['pend'])),
                'desc'      => $rs->fields['description'],
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
        return view('akunting.setup.periode_akunting.create');
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = PeriodeAkuntingMdl::save_periode_akunting();

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