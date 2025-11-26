<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class DaftarJurnalAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/DaftarJurnalMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'jurnal_speriod'    => get_var('jurnal_speriod', date('d-m-Y')),
            'jurnal_eperiod'    => get_var('jurnal_eperiod', date('d-m-Y')),
            'jtid'              => get_var('jtid'),
            'is_posted'         => get_var('is_posted'),
            'gldoc'             => get_var('gldoc'),
            'keterangan'        => get_var('keterangan'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = DaftarJurnalMdl::list($data, true)->RecordCount();
        $rs = DaftarJurnalMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'glid'          => $rs->fields['glid'],
                'jurnal_date'   => dbtstamp2stringina($rs->fields['gldate']),
                'journal_name'  => $rs->fields['journal_name'],
                'jurnal_doc'    => $rs->fields['gldoc'],
                'keterangan'    => $rs->fields['gldesc'],
                'is_posted'     => $rs->fields['is_posted'],
                'user_input'    => $rs->fields['useri'],
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

    public function detail_get ($myglid) /*{{{*/
    {
        $rsd = DaftarJurnalMdl::detail_jurnal($myglid);

        $data_db = !$rsd->EOF ? FieldsToObject($rsd->fields) : New stdClass();

        return view('akunting.daftar_jurnal.detail', compact(
            'myglid',
            'rsd',
            'data_db'
        ));
    } /*}}}*/
}
?>