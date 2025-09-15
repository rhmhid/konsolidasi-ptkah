<?php

use Luthier\MiddlewareInterface;
use Luthier\Auth;

class RememberMeMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     * 
     * @see \Luthier\MiddlewareInterface::run()
     */
    public function run($action = 'store')
    {
        if($action == 'store')
        {
            $this->storeAuthCookie();
        }
        elseif($action == 'restore')
        {
            $this->restoreAuthFromCookie();
        }
        elseif($action == 'destroy')
        {
            $this->destroyAuthCookie();
        }
        else
        {
            show_error('Unknown RememberMeMiddleware "' . $action . '" action');
        }
    }

    private function storeAuthCookie()
    {
        if(ci()->input->post(config_item('simpleauth_remember_me_field')) === null)
        {
            return;
        }

        ci()->load->library('encryption');

        $rememberToken = bin2hex(ci()->encryption->create_key(32));

        $db = ci()->adodb->init();

        $tbl_user = config_item('simpleauth_users_table');
        $field_user = config_item('simpleauth_remember_me_col');
        $pk_field_user = config_item('simpleauth_id_col');

        $sql = "UPDATE {$tbl_user} SET {$field_user} = '$rememberToken' WHERE {$pk_field_user} = ".Auth::user()->{$pk_field_user};
        $ok = $db->Execute($sql);

        ci()->input->set_cookie(config_item('simpleauth_remember_me_cookie'), $rememberToken, 1296000); // 15 days
    }

    private function restoreAuthFromCookie()
    {
        if( !Auth::isGuest() || Auth::session('fully_authenticated') === true)
        {
            return;
        }

        $db = ci()->adodb->init();
        ci()->load->helper('cookie');
        ci()->load->library('encryption');

        $rememberToken = get_cookie(config_item('simpleauth_remember_me_cookie'));

        if( empty($rememberToken) )
        {
            return;
        }

        $tbl_user = config_item('simpleauth_users_table');
        $field_user = config_item('simpleauth_remember_me_col');

        $sql = "SELECT * FROM {$tbl_user} WHERE {$field_user} = ".$rememberToken;
        $storedUserFromToken = $db->Execute($sql);

        if($storedUserFromToken->EOF)
        {
            delete_cookie(config_item('simpleauth_remember_me_cookie'));
            return;
        }

        $userProvider = Auth::loadUserProvider(config_item('simpleauth_user_provider'));

        try
        {
            $user = Auth::bypass($storedUserFromToken->fields[config_item('simpleauth_username_col')], $userProvider);
                    $userProvider->checkUserIsActive($user);

                    if(
                        config_item('simpleauth_enable_email_verification')  === TRUE &&
                        config_item('simpleauth_enforce_email_verification') === TRUE
                    )
                    {
                        $userProvider->checkUserIsVerified($user);
                    }
        }
        catch(\Exception $e)
        {
            delete_cookie(config_item('simpleauth_remember_me_cookie'));
            return;
        }

        Auth::store($user, ['fully_authenticated' => false]);
    }

    private function destroyAuthCookie()
    {
        ci()->load->helper('cookie');      
        delete_cookie(config_item('simpleauth_remember_me_cookie'));
    }
}