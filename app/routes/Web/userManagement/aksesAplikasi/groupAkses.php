<?php

Route::group('user-management/akses-aplikasi/group-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'UserManagement/AksesAplikasi/GroupAkses@index')->name('user_management.akses_aplikasi.group_akses');
});