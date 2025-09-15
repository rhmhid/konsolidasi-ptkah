<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class InboxAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Pesan/InboxMdl');
    } /*}}}*/

    public function index_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'first_new'     => get_var('first_new', false),
            'notification'  => get_var('notification', false),
            'start'         => get_var('start', 0),
            'length'        => 1, // get_var('length', PAGE_ROWS),
        );

        $jmlbris = InboxMdl::get_notif($data, true)->RecordCount();
        $rs = InboxMdl::get_notif($data, false, $data['start'], $data['length']);

        $no = 0;
        $record = [];
        while (!$rs->EOF)
        {
            $record[] = array(
                "no"            => $no++,
                "lid"           => $rs->fields['lid'],
                "nama_jenis"    => $rs->fields['nama_jenis'],
                "is_mobile"     => _fromMobile($rs->fields['is_mobile']),
                "create_time"   => $rs->fields['create_time'],
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

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            'first_new'     => get_var('first_new', false),
            'notification'  => get_var('notification', false),
            'start'         => get_var('start', 0),
            'length'        => 5,
        );

        $jmlbris = InboxMdl::list_myinbox($data, true)->RecordCount();
        $rs = InboxMdl::list_myinbox($data, false, $data['start'], $data['length']);

        $no = 0;
        $record = [];
        while (!$rs->EOF)
        {
            $record[] = array(
                "no"            => $no++,
                "lid"           => $rs->fields['lid'],
                "nama_jenis"    => $rs->fields['nama_jenis'],
                "is_mobile"     => _fromMobile($rs->fields['is_mobile']),
                "lapor_verif"   => $rs->fields['lapor_verif'],
                "create_time"   => $rs->fields['create_time'],
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
}
?>