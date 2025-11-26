<?php

Route::group('pengaturan-dasar/otorisasi-akses', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Konfigurasi/PengaturanDasar/OtorisasiAkses@index')->name('pengaturan_dasar.otorisasi_akses');
});