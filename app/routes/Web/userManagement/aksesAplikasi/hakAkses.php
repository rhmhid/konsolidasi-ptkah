<?php

Route::group('user-management/akses-aplikasi/hak-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'UserManagement/AksesAplikasi/HakAkses@index')->name('user_management.akses_aplikasi.hak_akses');
});