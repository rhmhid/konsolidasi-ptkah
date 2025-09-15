<?php

use Luthier\Auth;
use Luthier\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    final public function run ($userProvider)
    {
        $authLoginRoute = config_item('auth_login_route') !== null
            ? config_item('auth_login_route')
            : 'login';

        $authLoginRouteRedirect = config_item('auth_login_route_redirect') !== null
            ? config_item('auth_login_route_redirect')
            : null;

        $authLogoutRoute = config_item('auth_logout_route') !== null
            ? config_item('auth_logout_route')
            : 'logout';

        $authLogoutRouteRedirect = config_item('auth_logout_route_redirect') !== null
            ? config_item('auth_logout_route_redirect')
            : null;

        $curr_route = ci()->route->getName();
        $segment = ci()->uri->segment(1);
        $is_ajax = ci()->input->is_ajax_request();

        if (Auth::isGuest())
        {
            if ($curr_route != $authLoginRoute)
            {
                if ($segment == 'api' || $is_ajax == 't')
                    redirect( route('api.auth.timeout', ['type' => 'expired']) );
                else
                    redirect( route($authLoginRoute) );

                exit;
            }
        }
        else
        {
            if (Auth::user()->is_admin == 'f')
            {
                if (config_item('auth_multi_login') === FALSE)
                {
                    $token_check = Auth::user()->getAccessToken(Auth::session('__raw_token__'));

                    if ($curr_route != $authLoginRoute && !$token_check)
                    {
                        if ($segment == 'api' || $is_ajax == 't')
                            redirect( route('api.auth.timeout', ['type' => 'double']) );
                        else
                        {
                            ci()->session->set_flashdata('_auth_messages', [ 'danger' => 'ERR_LOGIN_SESSION_MULTIPLE' ]);

                            Auth::destroy();

                            redirect( route($authLoginRoute) );
                        }

                        exit;
                    }
                }

                if (ci()->auth_library->permissionExists($curr_route) && $curr_route != $authLoginRouteRedirect)
                {
                    if (!Auth::isGranted(Auth::user(), $curr_route))
                    {
                        access_denied();

                        exit;
                    }
                }
            }
        }
    }
}