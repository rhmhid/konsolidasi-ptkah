<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';

use Luthier\Auth;
use Luthier\Auth\ControllerInterface as AuthControllerInterface;

class AuthController extends BaseController implements AuthControllerInterface
{
    const ROOT_FOLDER = 'public/';

    public function __construct ()
    {
        parent::__construct();
    }

    final public function getUserProvider()
    {
        return 'MyUserProvider';
    }

    final public function getMiddleware()
    {
        return 'AuthenticateMiddleware';
    }

    public function login ()
    {
        $messages = Auth::messages();
        $referrer = $this->agent->referrer();

        $basic_url = str_replace(self::ROOT_FOLDER, '', base_url());
        $url_nothing_referrer = array(base_url(), route(config_item('auth_login_route_redirect')), $basic_url);
        $intended = !in_array($referrer, $url_nothing_referrer) ? str_replace(base_url(), '', $referrer) : '';

        return view('auth.login', compact('messages', 'intended'));
    }

    public function logout()
    {
        return;
    }

    public function signup ()
    {
        return view('auth.signup');
    }

    public function emailVerification($token)
    {
        return;
    }

    public function passwordReset()
    {
        return;
    }

    public function passwordResetForm($token)
    {
        return;
    }
}