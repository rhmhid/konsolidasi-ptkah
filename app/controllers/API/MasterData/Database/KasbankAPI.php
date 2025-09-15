<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class KasbankAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('MasterData/Database/KasbankMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'          => get_var('draw'),
            's_bank_type'   => get_var('s_bank_type'),
            's_kas_bank'    => get_var('s_kas_bank'),
            'start'         => get_var('start'),
            'length'        => get_var('length'),
        );

        $jmlbris = KasbankMdl::list($data, true)->RecordCount();
        $rs = KasbankMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "bank_id"           => $rs->fields['bank_id'],
                "bank_type"         => $rs->fields['bank_type'],
                "bank_nama"         => $rs->fields['bank_nama'],
                "bank_no_rek"       => $rs->fields['bank_no_rek'],
                "bank_atas_nama"    => $rs->fields['bank_atas_nama'],
                "bank_cabang"       => $rs->fields['bank_cabang'],
                'default_coa'       => $rs->fields['default_coa'],
                'ctrl_acc_coaid'    => $rs->fields['ctrl_acc_coaid'],
                'is_aktif'          => $rs->fields['is_aktif'],
                'status_txt'        => get_status_aktif($rs->fields['is_aktif']),
                'status_css'        => get_status_aktif($rs->fields['is_aktif'], 'css'),
                'status_icon'       => get_status_aktif($rs->fields['is_aktif'], 'icon'),
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
        $bank_id = get_var('bank_id', 0);

        $rsd = KasbankMdl::kas_bank_detail($bank_id);

        if (!$rsd->EOF)
        {
            $data_bank = FieldsToObject($rsd->fields);

            $is_cc = $data_bank->is_cc ?? 'f';

            $is_transfer = $data_bank->is_transfer ?? 'f';

            $is_petty_cash = $data_bank->is_petty_cash ?? 'f';

            $is_aktif = $data_bank->is_aktif ?? 'f';
        }
        else
        {
            $data_bank = New stdClass();

            $data_bank->bank_id = $bank_id;

            $is_cc = '';

            $is_transfer = '';

            $is_petty_cash = '';

            $is_aktif = 't';
        }

        $rs_bank_type = KasbankMdl::bank_type();
        $cmb_bank_type = $rs_bank_type->GetMenu2('bank_type', $data_bank->bank_type, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_type" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank Type" required=""');

        $rs_bank_image = KasbankMdl::bank_image();
        $cmb_bank_image = $rs_bank_image->GetMenu2('bank_img', $data_bank->bank_img, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_img" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank Image"');

        $rs_default_coa = KasbankMdl::bank_coa();
        $cmb_default_coa = $rs_default_coa->GetMenu2('default_coaid', $data_bank->default_coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="default_coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Default C.O.A" required=""');

        $rs_ctrl_coa = KasbankMdl::bank_coa();
        $cmb_ctrl_coa = $rs_ctrl_coa->GetMenu2('ctrl_acc_coaid', $data_bank->ctrl_acc_coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="ctrl_acc_coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Ctrl. Acc" ');

        $chk_cc = $is_cc == 't' ? 'checked=""' : '';
        $chk_transfer = $is_transfer == 't' ? 'checked=""' : '';
        $chk_petty_cash = $is_petty_cash == 't' ? 'checked=""' : '';

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('master_data.database.kas_bank.form', compact(
            'data_bank',
            'cmb_bank_type',
            'cmb_bank_image',
            'cmb_default_coa',
            'cmb_ctrl_coa',
            'chk_cc',
            'chk_transfer',
            'chk_petty_cash',
            'chk_aktif',
            'txt_aktif'
        ));
    } /*}}}*/

    public function cek_kode_post ($kode) /*{{{*/
    {
        $res = KasbankMdl::cek_kode($kode);

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
        $msg = KasbankMdl::save_kas_bank();

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