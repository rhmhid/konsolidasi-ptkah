<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class MataUangAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MataUangMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            's_kode_nama_curr'  => get_var('s_kode_nama_curr'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = MataUangMdl::list($data, true)->RecordCount();
        $rs = MataUangMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'cid'           => $rs->fields['cid'],
                'curr_code'     => $rs->fields['curr_code'],
                'curr_name'     => $rs->fields['curr_name'],
                'curr_desc'     => $rs->fields['curr_desc'],
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
        $cid = get_var('cid', 0);
        $is_aktif = 't';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.setup.mata_uang.form', compact(
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function edit_get () /*{{{*/
    {
        $cid = get_var('cid', 0);
            
        $rs = MataUangMdl::currency_detail($cid);

        $data_curr = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $chk_aktif = $data_curr->is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($data_curr->is_aktif);

        return view('akunting.setup.mata_uang.form', compact(
            'data_curr',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    function cek_kode_post ($kode) /*{{{*/
    {
        $res = MataUangMdl::cek_kode($kode);

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
        $msg = MataUangMdl::save_currency();

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

    function rates_get () /*{{{*/
    {
        $cid = get_var('cid', 0);
        $curr_start = date('d-m-Y H:i');
        $curr_rate = 1;
        $is_aktif = 't';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('akunting.setup.mata_uang.rates', compact(
            'cid',
            'curr_start',
            'curr_rate',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function list_data_rates_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'cid'       => get_var('cid'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = MataUangMdl::list_rates($data, true)->RecordCount();
        $rs = MataUangMdl::list_rates($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'curr_start'    => dbtstamp2stringlong_ina($rs->fields['curr_start']),
                'curr_rate'     => format_uang($rs->fields['curr_rate'], 2),
                'create_by'     => $rs->fields['create_by'],
                'create_time'   => dbtstamp2stringlong_ina($rs->fields['create_time']),
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

    public function save_rates_patch () /*{{{*/
    {
        $msg = MataUangMdl::save_currency_rate();

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