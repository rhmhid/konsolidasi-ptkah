<?php

Route::group('api/user-management/akses-aplikasi/group-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/UserManagement/AksesAplikasi/GroupAksesAPI@list_data')->name('api.user_management.akses_aplikasi.group_akses');
    Route::get('/create', 'API/UserManagement/AksesAplikasi/GroupAksesAPI@form')->name('api.user_management.akses_aplikasi.group_akses.create');
    Route::get('/edit', 'API/UserManagement/AksesAplikasi/GroupAksesAPI@form')->name('api.user_management.akses_aplikasi.group_akses.edit');
    Route::post('/cek-kode/{kode}', 'API/UserManagement/AksesAplikasi/GroupAksesAPI@cek_kode')->name('api.user_management.akses_aplikasi.group_akses.cek_kode');
    Route::patch('/save', 'API/UserManagement/AksesAplikasi/GroupAksesAPI@save')->name('api.user_management.akses_aplikasi.group_akses.save');
});