<?php

/**
 * --------------------------------------------------------------------------------------
 *                                General Auth configuration
 * --------------------------------------------------------------------------------------
 */

//
// Basic routing
//

$config['auth_login_route']  = 'login';

$config['auth_logout_route'] = 'logout';

$config['auth_login_route_redirect'] = 'homepage';

$config['auth_logout_route_redirect'] = 'login';

$config['auth_route_auto_redirect'] = [

    # The following routes will redirect to the 'auth_login_route_redirect' if
    # the user is logged in:

    'login',
    'signup',
    'password_reset',
    'password_reset_form'
];

//
// Main login form
//

$config['auth_form_username_field'] = 'username';

$config['auth_form_password_field'] = 'password';

//
// Session & Cookies configuration
//

$config['auth_session_var'] = env('SESSION_NAME');


/**
 * --------------------------------------------------------------------------------------
 *                                SimpleAuth configuration
 * --------------------------------------------------------------------------------------
 */

//
// Enable/disable features
//

$config['simpleauth_enable_signup'] = FALSE;

$config['simpleauth_enable_password_reset'] = FALSE;

$config['simpleauth_enable_remember_me'] = TRUE;

$config['simpleauth_enable_email_verification'] = FALSE;

$config['simpleauth_enforce_email_verification'] = FALSE;

$config['simpleauth_enable_brute_force_protection'] = TRUE;

$config['simpleauth_enable_acl'] = TRUE;

//
// Views configuration
//

$config['simpleauth_skin'] = 'default';

$config['simpleauth_assets_dir'] = 'assets/auth';

//
// ACL Configuration
//

$config['simpleauth_acl_map'] = [

    // If you are worried about performance, you can fill this array with $key => $value
    // pairs of known permissions/permissions groups ids, reducing drastically the
    // amount of executed database queries
    //
    // Example
    //    [ permission full name ]       [ permission id ]
    //    'general.blog.read'        =>         1
    //    'general.blog.edit'        =>         2
    //    'general.blog.delete'      =>         3
];

//
// Email configuration
//

$config['simpleauth_email_configuration'] = null;

$config['simpleauth_email_address'] = 'noreply@example.com';

$config['simpleauth_email_name'] = 'Example';

$config['simpleauth_email_verification_message'] = NULL;

$config['simpleauth_password_reset_message'] = NULL;

//
// Remember me configuration
//

$config['simpleauth_remember_me_field'] = 'remember_me';

$config['simpleauth_remember_me_cookie'] = 'remember_me';

//
// Database configuration
//

$config['simpleauth_user_provider'] = 'MyUser';

$config['simpleauth_users_table']  = 'app_users';

$config['simpleauth_users_email_verification_table']  = 'email_verifications';

$config['simpleauth_password_resets_table']  = 'password_resets';

$config['simpleauth_login_attempts_table']  = 'login_attempts';

$config['simpleauth_max_login_attempts']  = 3;

$config['simpleauth_minutes_login_attempts']  = 5; # 5 minutes

$config['simpleauth_time_login_attempts']  = 60 * $config['simpleauth_minutes_login_attempts'];

$config['simpleauth_users_acl_table']  = 'role_group';

$config['simpleauth_users_acl_categories_table']  = 'menus';

$config['simpleauth_id_col'] = 'asid';

$config['simpleauth_username_col'] = 'username';

$config['simpleauth_email_col']  = 'email';

$config['simpleauth_email_first_name_col'] = 'first_name';

$config['simpleauth_password_col'] = 'userpass';

$config['simpleauth_role_col'] = 'is_admin';

$config['simpleauth_active_col'] = 'is_active';

$config['simpleauth_verified_col'] = 'verified';

$config['simpleauth_remember_me_col'] = 'remember_token';

$config['ERR_LOGIN_INVALID_CREDENTIALS'] = 'Username / Password yang anda inputkan tidak ditemukan dalam sistem.';

$config['ERR_LOGIN_INACTIVE_USER'] = 'User yang anda inputkan sudah dinonaktifkan dalam database sistem.';

// $config['ERR_LOGIN_UNVERIFIED_USER'] = 'User yang anda inputkan diperlukan verifikasi.';
$config['ERR_LOGIN_UNVERIFIED_USER'] = 'User yang anda inputkan belum diassign ke cabang.';

$config['ERR_LOGIN_ATTEMPT_BLOCKED'] = 'User yang anda inputkan terblokir selama '.$config['simpleauth_minutes_login_attempts'].' menit.';

$config['ERR_LOGIN_SESSION_EXPIRED'] = 'Sesi login sudah berakhir. Silahkan login ulang untuk melanjutkan penggunaan aplikasi.';

$config['ERR_LOGIN_SESSION_MULTIPLE'] = 'Sesi login anda telah diakhiri karena terdeteksi multiple login dalam 1 akun yang sama.';

$config['auth_multi_login'] = TRUE; # Jika FALSE, maka user selain is_admin = TRUE tidak bisa dipakai berbarengan dalam waktu bersamaan

$config['auth_history_login'] = TRUE;

$config['auth_intended_login'] = FALSE; # Jika TRUE, maka ketika login sukses redirect ke halaman terakhir sebelum logout

$config['simpleauth_users_token_table'] = 'app_users_token';

$config['simpleauth_users_token_name'] = 'auth_token';

$config['simpleauth_users_token_expired'] = env('SESSION_LIFETIME');