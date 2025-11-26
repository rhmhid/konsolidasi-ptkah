<?php

Route::group('api/user-management/akses-aplikasi/hak-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/UserManagement/AksesAplikasi/HakAksesAPI@list_data')->name('api.user_management.akses_aplikasi.hak_akses');
    Route::get('/create', 'API/UserManagement/AksesAplikasi/HakAksesAPI@create')->name('api.user_management.akses_aplikasi.hak_akses.create');
    Route::get('/cari-pegawai', 'API/UserManagement/AksesAplikasi/HakAksesAPI@cari_pegawai')->name('api.user_management.akses_aplikasi.hak_akses.cari_pegawai');
    Route::post('/cek-user/{kode}', 'API/UserManagement/AksesAplikasi/HakAksesAPI@cek_user')->name('api.user_management.akses_aplikasi.hak_akses.cek_user');
    Route::patch('/save', 'API/UserManagement/AksesAplikasi/HakAksesAPI@save')->name('api.user_management.akses_aplikasi.hak_akses.save');
    Route::get('/edit', 'API/UserManagement/AksesAplikasi/HakAksesAPI@edit')->name('api.user_management.akses_aplikasi.hak_akses.edit');
    Route::patch('/update', 'API/UserManagement/AksesAplikasi/HakAksesAPI@update')->name('api.user_management.akses_aplikasi.hak_akses.update');
    Route::get('/edit_pass', 'API/UserManagement/AksesAplikasi/HakAksesAPI@edit_pass')->name('api.user_management.akses_aplikasi.hak_akses.edit_pass');
    Route::patch('/update_pass', 'API/UserManagement/AksesAplikasi/HakAksesAPI@update_pass')->name('api.user_management.akses_aplikasi.hak_akses.update_pass');
});