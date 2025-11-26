<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class PeriodeTarifAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/PolaTarif/PeriodeTarifMdl');
    } /*}}}*/

    public function list_data_period_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            's_kode_nama'   => get_var('s_kode_nama'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = PeriodeTarifMdl::list($data, true)->RecordCount();
        $rs = PeriodeTarifMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "periode_id"    => $rs->fields['periode_id'],
                "periode_code"  => $rs->fields['periode_code'],
                "periode_name"  => $rs->fields['periode_name'],
                "periode_start" => dbtstamp2stringlong_ina($rs->fields['periode_start']),
                "is_aktif"      => $rs->fields['is_active'],
                'status_txt'    => get_status_aktif($rs->fields['is_active']),
                'status_css'    => get_status_aktif($rs->fields['is_active'], 'css'),
                'status_icon'   => get_status_aktif($rs->fields['is_active'], 'icon'),
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

    public function form_get () /*{{{*/
    {
        $periode_id = get_var('periode_id', 0);

        $rsd = PeriodeTarifMdl::periode_tarif_detail($periode_id);

        if (!$rsd->EOF)
        {
            $data_period = FieldsToObject($rsd->fields);

            $data_period->periode_start = date('d-m-Y H:i', strtotime($data_period->periode_start));

            $is_active = $data_period->is_active ?? 'f';
        }
        else
        {
            $data_period = New stdClass();

            $data_period->periode_id = $periode_id;

            $is_active = 't';
        }

        $chk_aktif = $is_active == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_active);

        return view('master_data.pola_tarif.periode_tarif.form', compact(
            'data_period',
            'chk_aktif',
            'txt_aktif',
            'txt_title'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = PeriodeTarifMdl::cek_kode($kode);

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

    public function save_period_patch ()
    {
        $msg = PeriodeTarifMdl::save_periode_tarif();

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
    }
}
?>