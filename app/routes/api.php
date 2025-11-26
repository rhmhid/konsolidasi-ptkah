<?php

/**
 * API Routes
 *
 * This routes only will be available under AJAX requests. This is ideal to build APIs.
 */

Route::match(['get', 'post'], 'api/auth-timeout/{type}', 'API/Auth/AuthAPI@auth_timeout')->name('api.auth.timeout');

Route::group('api/auth-change-password', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'API/Auth/AuthAPI@change_password')->name('api.auth.change_password');
    Route::patch('/save', 'API/Auth/AuthAPI@save_change_password')->name('api.auth.change_password.save');
});

Route::group('api/auth/otorisasi-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'API/Auth/AuthAPI@otorisasi_akses')->name('api.auth.otorisasi_akses');
    Route::patch('/save', 'API/Auth/AuthAPI@save_otorisasi_akses')->name('api.auth.otorisasi_akses.save');
});

Route::group('api/auth-change-branch', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'API/Auth/AuthAPI@change_branch')->name('api.auth.change_branch');
    Route::get('cari-branch', 'API/Auth/AuthAPI@cari_branch')->name('api.auth.change_branch.cari_branch');
    Route::patch('/save', 'API/Auth/AuthAPI@save_change_branch')->name('api.auth.change_branch.save');
});

require_once APPPATH . '/libraries/Auth_library.php';

$files = Auth_library::glob_recursive(APPPATH.'/routes/API/*.php');

foreach ($files as $file) require_once $file;