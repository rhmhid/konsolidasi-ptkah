<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseAPIController.php';

class AuthAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Auth/AuthMdl');
    } /*}}}*/

    public function auth_timeout_get ($type) /*{{{*/
    {
        $this->auth_timeout($type);
    } /*}}}*/

    public function auth_timeout_post ($type) /*{{{*/
    {
        $this->auth_timeout($type);
    } /*}}}*/

    public function auth_timeout ($type) /*{{{*/
    {
        if ($type == 'double')
            $message = config_item('ERR_LOGIN_SESSION_MULTIPLE');
        else
            $message = config_item('ERR_LOGIN_SESSION_EXPIRED');

        $this->response( [
            'data'      => [
                'authenthicated'    => false,
            ],
            'message'   => $message,
        ], REST::HTTP_UNAUTHORIZED );
    } /*}}}*/

    public function change_password_get () /*{{{*/
    {
        return view('auth.change_password');
    } /*}}}*/

    public function save_change_password_patch () /*{{{*/
    {
        $msg = AuthMdl::save_change_password();

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

    public function otorisasi_akses_get () /*{{{*/
    {
        $otogid = get_var('otogid');
        $frm = get_var('frm');
        $notes = get_var('notes');
        $pid = Auth::user()->pid;

        $otoid = AuthMdl::get_otorisasi_by_user($otogid, $pid);

        $subtitle = AuthMdl::get_otorisasi_group_by_user($otogid);

        $rs_user_auth = AuthMdl::get_user_by_otorisasi($otogid);
        $cmb_user_auth = $rs_user_auth->GetMenu2('otoid', $otoid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100 select-auth-user" id="otoid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih User" required=""');

        return view('auth.otorisasi_akses', compact(
            'otogid',
            'frm',
            'notes',
            'subtitle',
            'cmb_user_auth'
        ));
    } /*}}}*/

    public function save_otorisasi_akses_patch () /*{{{*/
    {
        $msg = AuthMdl::save_otorisasi_akses();

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

    public function change_branch_get () /*{{{*/
    {
        return view('auth.change_branch');
    } /*}}}*/

    public function cari_branch_get () /*{{{*/
    {
        $pid = Auth::user()->pid;

        $res = Modules::availableBranch($pid);

        $dtJSON = array();
        while (!$res->EOF)
        {
            $dtJSON[] = $res->fields;

            $res->MoveNext();
        }

        $this->response($dtJSON, REST::HTTP_OK);
    } /*}}}*/

    public function save_change_branch_patch () /*{{{*/
    {
        $msg = AuthMdl::save_change_branch();

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