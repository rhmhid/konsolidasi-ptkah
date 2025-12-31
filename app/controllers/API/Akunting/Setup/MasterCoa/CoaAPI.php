<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class CoaAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/Setup/MasterCoa/CoaMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            's_coatid'  => get_var('s_coatid'),
            'start'     => get_var('start'),
            // 'length'    => get_var('length'),
            'length'    => 500,
        );

        $jmlbris = CoaMdl::list($data['s_coatid'], true)->RecordCount();
        $rs = CoaMdl::list($data['s_coatid'], false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $space = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $rs->fields['level']);

            $coa = $rs->fields['allow_post'] == 't' ? $rs->fields['coa'] : '<b>'.$rs->fields['coa'].'</b>';

            $mapping_pos = $rs->fields['coatid'] < 4 ? $rs->fields['pos_na'] : $rs->fields['pos_pl'];

            $record[] = array(
                'coaid'         => encrypt($rs->fields['coaid']),
                'coatid'        => $rs->fields['coatid'],
                'coa'           => $space.$coa,
                'header_coa'    => $rs->fields['allow_post'] == 't' ? 'No' : 'Yes',
                'default_coa'   => $rs->fields['default_debet'] == 't' ? 'Dr' : 'Cr',
                'postable_coa'  => $rs->fields['allow_post'] == 't' ? 'Yes' : 'No',
                'valid_coa'     => $rs->fields['is_valid'] == 't' ? 'Yes' : 'No',
                'group_coa'     => $rs->fields['coa_group'],
                'mapping_pos'   => $mapping_pos,
                'mapping_cf'    => $rs->fields['pos_cf'],
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
        $data_coa = New stdClass();

        $data_coa->coatid = get_var('coatid', 0);
        $data_coa->coaid = get_var('coaid', 0, 't');
        $data_coa->is_valid = 't';

        $rs_group_coa = CoaMdl::group_coa();
        $cmb_group_coa = $rs_group_coa->GetMenu2('coagid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coagid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Group C.O.A" required=""');

        $rs_parent_coa = CoaMdl::parent_coa($data_coa->coatid);
        $cmb_parent_coa = $rs_parent_coa->GetMenu2('parent_coaid', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="parent_coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Parent C.O.A"');

        if ($data_coa->coatid < 4)
        {
            $jenis_pos = $data_coa->coatid == 1 ? 1 : 2;

            $cmb_pos_na = Modules::pos_na('pnid', $data_coa->pnid, $jenis_pos);

            $show_pos_na = TRUE;
            $show_pos_pl = FALSE;
        }
        else
        {
            $cmb_pos_pl = Modules::pos_pl('pplid', $data_coa->pplid, $data_coa->coatid);

            $show_pos_na = FALSE;
            $show_pos_pl = TRUE;
        }

        $cmb_pos_cf = Modules::pos_cf('pcfdid', $data_coa->pcfdid, 1);

        $txt_aktif = get_status_aktif($data_coa->is_valid);

        return view('akunting.setup.master_coa.coa.form', compact(
            'data_coa',
            'cmb_group_coa',
            'cmb_parent_coa',
            'cmb_pos_na',
            'cmb_pos_pl',
            'cmb_pos_cf',
            'txt_aktif',
            'show_pos_na',
            'show_pos_pl'
        ));
    } /*}}}*/

    public function edit_get () /*{{{*/
    {
        $coatid = get_var('coatid', 0);
        $coaid = get_var('coaid', 0, 't');

        $rs = CoaMdl::coa_detail($coaid);

        $data_coa = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $rs_group_coa = CoaMdl::group_coa();
        $cmb_group_coa = $rs_group_coa->GetMenu2('coagid', $data_coa->coagid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coagid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Group C.O.A" required=""');

        $rs_parent_coa = CoaMdl::parent_coa($data_coa->coatid, $data_coa->coaid);
        $cmb_parent_coa = $rs_parent_coa->GetMenu2('parent_coaid', $data_coa->parent_coaid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="parent_coaid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Parent C.O.A"');

        if ($data_coa->coatid < 4)
        {
            $jenis_pos = $data_coa->coatid == 1 ? 1 : 2;

            $cmb_pos_na = Modules::pos_na('pnid', $data_coa->pnid, $jenis_pos);

            $show_pos_na = TRUE;
            $show_pos_pl = FALSE;
        }
        else
        {
            $cmb_pos_pl = Modules::pos_pl('pplid', $data_coa->pplid, $data_coa->coatid);

            $show_pos_na = FALSE;
            $show_pos_pl = TRUE;
        }

        $sel_def_dr = $data_coa->default_debet == 't' ? 'selected=""' : '';
        $sel_def_cr = $data_coa->default_debet == 't' ? '' : 'selected=""';
        $sel_allow_t = $data_coa->allow_post == 't' ? 'selected=""' : '';
        $sel_allow_f = $data_coa->allow_post == 't' ? '' : 'selected=""';
        $cmb_pos_cf = Modules::pos_cf('pcfdid', $data_coa->pcfdid, 1);
        $chk_reset = $data_coa->period_reset == 't' ? 'checked=""' : '';
        $chk_petty_cash = $data_coa->is_petty_cash == 't' ? 'checked=""' : '';
        $chk_manual_journal = $data_coa->is_manual_journal == 't' ? 'checked=""' : '';
        $chk_valid = $data_coa->is_valid == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($data_coa->is_valid);

        return view('akunting.setup.master_coa.coa.form', compact(
            'data_coa',
            'cmb_group_coa',
            'sel_def_dr',
            'sel_def_cr',
            'sel_allow_t',
            'sel_allow_f',
            'cmb_parent_coa',
            'cmb_pos_na',
            'cmb_pos_pl',
            'cmb_pos_cf',
            'chk_reset',
            'chk_petty_cash',
            'chk_manual_journal',
            'chk_valid',
            'txt_aktif',
            'show_pos_na',
            'show_pos_pl'
        ));
    } /*}}}*/

    public function save_patch () /*{{{*/
    {
        $msg = CoaMdl::save_coa();

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Diproses'
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $msg
            );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/

    public function mapping_get () /*{{{*/
    {
        $coaid = get_var('coaid', 0, 't');

        $rs = CoaMdl::coa_detail($coaid);

        $data_coa = !$rs->EOF ? FieldsToObject($rs->fields) : New stdClass();

        $rs_cabang = CoaMdl::coa_cabang($coaid);

        $data_coa_cabang = !$rs_cabang->EOF ? FieldsToObject($rs_cabang->fields) : New stdClass();

        return view('akunting.setup.master_coa.coa.mapping', compact(
           'rs',
           'data_coa',
           'rs_cabang',
           'data_coa_cabang'
        ));
    } /*}}}*/

    public function save_mapping_patch () /*{{{*/
    {
        $msg = CoaMdl::save_mapping();

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Diproses'
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