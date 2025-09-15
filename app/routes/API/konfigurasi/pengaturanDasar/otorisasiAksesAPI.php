<?php

Route::group('api/pengaturan-dasar/otorisasi-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Konfigurasi/PengaturanDasar/OtorisasiAksesAPI@list_data')->name('api.pengaturan_dasar.otorisasi_akses');
    Route::get('/create', 'API/Konfigurasi/PengaturanDasar/OtorisasiAksesAPI@create')->name('api.pengaturan_dasar.otorisasi_akses.create');
    Route::get('/cari-user', 'API/Konfigurasi/PengaturanDasar/OtorisasiAksesAPI@cari_user')->name('api.pengaturan_dasar.otorisasi_akses.cari_user');
    Route::patch('/save', 'API/Konfigurasi/PengaturanDasar/OtorisasiAksesAPI@save')->name('api.pengaturan_dasar.otorisasi_akses.save');
    Route::patch('/delete', 'API/Konfigurasi/PengaturanDasar/OtorisasiAksesAPI@delete')->name('api.pengaturan_dasar.otorisasi_akses.delete');
});