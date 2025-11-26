<?php

use Luthier\Auth\Middleware as AuthMiddleware;
use Luthier\Auth\UserInterface;
use Luthier\Route;

require APPPATH . '/middleware/RememberMeMiddleware.php';

class AuthenticateMiddleware extends AuthMiddleware
{
    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::preLogin()
     */
    public function preLogin (Route $route)
    {
        if (
            $route->getName()     == config_item('auth_login_route') &&
            $route->requestMethod == 'POST' &&
            config_item('simpleauth_enable_brute_force_protection') === true
        )
        {
            $db = ci()->adodb->init();

            $tbl_login = config_item('simpleauth_login_attempts_table');

            $sdate = date('Y-m-d H:i:s', time() - config_item('simpleauth_time_login_attempts'));
            $edate = date('Y-m-d H:i:s', time());
            $ip_addr = ci()->input->ip_address();
            $userpost = ci()->input->post('username');
            $username = $db->qstr($userpost);

            // set username again
            ci()->session->set_flashdata('_auth_username', $userpost);

            $sql = "SELECT COUNT(*) FROM {$tbl_login} WHERE ip = ? AND username = $username AND created_at >= ? AND created_at <= ?";
            $loginAttemptCount = $db->GetOne($sql, array($ip_addr, $sdate, $edate));

            if ($loginAttemptCount >= config_item('simpleauth_max_login_attempts'))
            {
                ci()->session->set_flashdata('_auth_messages', [ 'danger' => 'ERR_LOGIN_ATTEMPT_BLOCKED' ]);

                return redirect(route(config_item('auth_login_route')));
            }
        }
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginSuccess()
     */
    public function onLoginSuccess(UserInterface $user)
    {
        if ( config_item('simpleauth_enable_remember_me') === true )
        {
            ci()->middleware->run( new RememberMeMiddleware(), 'store');
        }

        $db = ci()->adodb->init();
        $tbl_user = config_item('simpleauth_users_table');
        $sess_path = config_item('sess_save_path');
        $sess_id = session_id();
        $ip_addr = ci()->input->ip_address();
        $asid = Auth::user()->asid;

        $db->debug = true;

        if (config_item('auth_multi_login') === FALSE && Auth::user()->is_admin == 'f')
        {
            Auth::user()->revokeAccessTokenName(config_item('simpleauth_users_token_name'));
        }

        $result_token = Auth::user()->generateAccessToken(config_item('simpleauth_users_token_name'));

        Auth::user()->withAccessToken(
            Auth::user()->getAccessToken(
                $result_token->raw_token
            )
        );

        Auth::session('__raw_token__', $result_token->raw_token);
        Auth::session('__token__', $result_token->token);

        // B: Branch selection.
        $countAvailableBranch = Modules::countUserAvailableBranch(Auth::user()->pid);
        $branch = self::_selectBranch($user, $countAvailableBranch);

        $sqlb = "SELECT branch_name, branch_addr, branch_logo FROM branch WHERE bid = ?";
        $rs_branch = $db->Execute($sqlb, [$branch]);

        $data_branch = array(
            'bid'           => $branch,
            'branch_name'   => $rs_branch->fields['branch_name'],
            'branch_addr'   => $rs_branch->fields['branch_addr'],
            'branch_logo'   => $rs_branch->fields['branch_logo'],
        );

        $_SESSION[config_item('auth_session_var')]['user']['entity']->branch = FieldsToObject($data_branch);
        // E: Branch selection.

        $sqlu = "UPDATE {$tbl_user} SET last_ip = '$ip_addr', last_login = NOW(), token = '{$result_token->token}', last_login_bid = $branch WHERE pid = ?";
        $ok = $db->Execute($sqlu, array(Auth::user()->pid));

        if (config_item('auth_history_login') === TRUE)
        {
            $sqli = "INSERT INTO app_users_logs (asid, token, ip_address, bid) VALUES ($asid, '{$result_token->token}', '$ip_addr', $branch)";
            if ($ok) $ok = $db->Execute($sqli);
        }

        // update session
        $sqlu = "UPDATE {$sess_path} SET asid = $asid, login_date = NOW() WHERE id = ?";
        if ($ok) $ok = $db->Execute($sqlu, array($sess_id));

        if (ci()->input->post('intended') && config_item('auth_intended_login'))
            $next_url = ci()->input->post('intended');
        else
            $next_url = route_exists(config_item('auth_login_route_redirect'))
                        ? route(config_item('auth_login_route_redirect'))
                        : base_url();

        return redirect($next_url);
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginFailed()
     */
    public function onLoginFailed($username)
    {
        $db = ci()->adodb->init();

        if ( config_item('simpleauth_enable_brute_force_protection') === true )
        {
            $tbl_login = config_item('simpleauth_login_attempts_table');
            $ip_addr = ci()->input->ip_address();

            $sql = "INSERT INTO {$tbl_login} (username, ip) VALUES ('$username', '$ip_addr')";
            $ok = $db->Execute($sql);
        }
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginInactiveUser()
     */
    public function onLoginInactiveUser(UserInterface $user)
    {
        return;
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLoginUnverifiedUser()
     */
    public function onLoginUnverifiedUser(UserInterface $user)
    {
        return;
    }

    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\Auth\Middleware::onLogout()
     */
    public function onLogout ()
    {
        ci()->middleware->run( new RememberMeMiddleware(), 'destroy');
    }

    private static function _selectBranch ($user, $countAvailableBranch) /*{{{*/
    {
        $isMultiTenants = isMultiTenants();
        $val = 'f';
        $branch_active = NULL;
        $valOpenKonfigurasiModal = 'f';

        if ($user->last_login_bid == '') $valOpenKonfigurasiModal = 't';
        else $branch_active = $user->last_login_bid;

        if ($isMultiTenants == 'f') {
            if ($user->last_login_bid == '' || $user->last_login_bid != 1)
            {
                // self::assignBranch($user->pid, 1);
            }

            // Set Session
            $branch_active = 1;
        } else {
            $userBranchActive = Modules::isUserBranchActive($user->pid);

            if ($countAvailableBranch > 1)
                $val = 't';

            if ($user->last_login_bid == '' || !$userBranchActive)
            {
                $availableBranch = Modules::availableBranch($user->pid);

                $firstBranch = $availableBranch->fields;

                // self::assignBranch($user->pid, $firstBranch['bid']);

                // Set Session
                $branch_active = $firstBranch['bid'];
            }
        }

        if ($valOpenKonfigurasiModal == 't')
        {
            ci()->session->set_flashdata('__openKonfigurasi__', $valOpenKonfigurasiModal);
            $val = 'f';
        }

        ci()->session->set_flashdata('__openBranch__', $val);

        return $branch_active;
    }
}