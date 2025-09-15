<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class GroupAksesAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('UserManagement/AksesAplikasi/GroupAksesMdl');
    } /*}}}*/

    public function list_data_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'kode_nama_group'   => get_var('kode_nama_group'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = GroupAksesMdl::list($data, true)->RecordCount();
        $rs = GroupAksesMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "rgid"          => encrypt($rs->fields['rgid']),
                "role_kode"     => $rs->fields['role_kode'],
                "role_name"     => $rs->fields['role_name'],
                "is_aktif"      => $rs->fields['is_aktif'],
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

    public function form_get () /*{{{*/
    {
        $rgid = get_var('rgid', 0, 't');

        $old_parent = '';
        $module = '';

        $rsd = GroupAksesMdl::group_akses_detail($rgid);

        if (!$rsd->EOF)
        {
            $data_group = FieldsToObject($rsd->fields);

            $role_mid = explode(',', $data_group->role_mid);

            $is_aktif = $data_group->is_aktif ?? 'f';
        }
        else
        {
            $data_group = New stdClass();

            $role_mid = [];

            $is_aktif = 't';
        }

        $rs = GroupAksesMdl::list_module();

        $param = [];
        $child = [];
        $parent = [];

        while (!$rs->EOF)
        {
            $param[$rs->fields['mid']] = $rs->fields;

            if ($rs->fields['parent_mid'] != '')
                $child[$rs->fields['mid']] = $rs->fields['parent_mid'];
            else
                $parent[] = $rs->fields['mid'];

            $rs->MoveNext();
        }

        $list_module = '';

        foreach ($parent as $k => $v)
        {
            $submodule = '';

            foreach ($child as $x => $y)
            {
                if ($y == $v)
                {
                    $submodule .= '<div class="list-modul">
                                        '.self::_loop_menu_row($x, $param, $parent, $child, $role_mid).'
                                    </div>';

                }
            }

            $list_module .= '<div class="accordion-item">
                                <h2 class="accordion-header" id="kt_accordion_menu_header_'.$v.'">
                                    <button class="accordion-button fs-4 fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_menu_body_'.$v.'" aria-expanded="true" aria-controls="kt_accordion_menu_body_'.$v.'">
                                        '.$param[$v]['title'].'
                                    </button>
                                </h2>

                                <div id="kt_accordion_menu_body_'.$v.'" class="accordion-collapse collapse" aria-labelledby="kt_accordion_menu_header_'.$v.'" data-bs-parent="#kt_accordion_menu">
                                    <div class="accordion-body">
                                        '.$submodule.'
                                    </div>
                                </div>
                            </div>';
        }

        $chk_aktif = $is_aktif == 't' ? 'checked=""' : '';
        $txt_aktif = get_status_aktif($is_aktif);

        return view('user_management.akses_aplikasi.group_akses.form', compact(
            'data_group',
            'chk_aktif',
            'txt_aktif',
            'list_module'
        ));
    } /*}}}*/

    static function _loop_menu_row ($mid, $param, $parent, $child, $role_mid) /*{{{*/
    {
        $class_mod = $data_group = $submodule = $icon_sub = $attr_cbx = '';
        $level = $param[$mid]['level'];

        $space_level = array(
            2 => '',
            3 => 'ms-5',
            4 => 'ms-10',
        );

        foreach ($child as $x => $y)
        {
            if ($y == $mid)
                $submodule .= self::_loop_menu_row($x, $param, $parent, $child, $role_mid);
        }

        if ($level > 2)
        {
            $data_id = $mid;
            $class_chk = 'm_submodule';
            $data_group = 'data-group_mid="'.$param[$mid]['group_mid'].'"';

            if ($param[$mid]['is_header'] == 't')
                $attr_cbx = 'data-is_header="'.$param[$mid]['is_header'].'"';
            else
                $attr_cbx = 'data-parent_mid="'.$param[$mid]['parent_mid'].'"';
        }
        else
        {
            $data_id = $mid;
            $class_mod = 'moduls';
            $class_chk = 'm_module';

            if ($submodule)
                $icon_sub = '<div class="pt-1 icon cursor-pointer" mid="'.$mid.'">
                                <i class="fas fa-chevron-down"></i>
                            </div>';
        }

        $chk_mid = in_array($mid, $role_mid) ? 'checked=""' : '';

        $module .= '<div class="tree-view-menus card shadow-sm mb-4 '.$space_level[$level].'">
                        <div class="py-3 px-5 d-flex justify-content-between '.$class_mod.'">
                            <div class="d-flex fv-row">
                                <!--begin::Radio-->
                                <div class="form-check form-check-custom form-check-solid">
                                    <!--begin::Input-->
                                    <input class="form-check-input me-3 '.$class_chk.'" type="checkbox" name="mid['.$data_id.']" id="mid['.$data_id.']" value="'.$mid.'" '.$data_group.' '.$attr_cbx.' '.$chk_mid.' />
                                    <!--end::Input-->

                                    <!--begin::Label-->
                                    <label class="form-check-label" for="mid['.$data_id.']">
                                        <div class="fw-bolder text-gray-800">'.$param[$mid]['title'].'</div>
                                    </label>
                                    <!--end::Label-->
                                </div>
                                <!--end::Radio-->
                            </div>

                            '.$icon_sub.'
                        </div>
                    </div>';
        
        if ($level == 2)
            $module .= "\n<div class='submodul collapse mid-".$data_id."'>";

        $module .= "\n".$submodule;

        if ($level == 2)
            $module .= "\n</div>";

        return $module;
    } /*}}}*/

    function cek_kode_post ($kode) /*{{{*/
    {
        $res = GroupAksesMdl::cek_kode($kode);

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
        $msg = GroupAksesMdl::save_group_akses();

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