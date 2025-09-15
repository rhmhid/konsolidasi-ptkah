<?php

Route::group('migrasi-data', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Konfigurasi/MigrasiData@index')->name('migrasi_data');

    Route::get('/reset-data', 'Konfigurasi/MigrasiData@reset_data')->name('migrasi_data.reset_data');

    Route::get('/updol/pegawai', 'Konfigurasi/MigrasiData@updol_pegawai')->name('migrasi_data.updol.pegawai');

    Route::get('/updol/user', 'Konfigurasi/MigrasiData@updol_user')->name('migrasi_data.updol.user');
    Route::get('/download/user/{type}', 'Konfigurasi/MigrasiData@download_akses')->name('migrasi_data.download.user');
    Route::get('/download/file/{xls}', 'Konfigurasi/MigrasiData@download_file')->name('migrasi_data.download_file');

    Route::get('/import-barang', 'Konfigurasi/MigrasiData@import_barang')->name('migrasi_data.import_barang');
    Route::get('/import-stok', 'Konfigurasi/MigrasiData@import_stok')->name('migrasi_data.import_stok');
});

Route::group('migrasi-data/akunting', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/import-tb', 'Konfigurasi/MigrasiData@import_tb')->name('migrasi_data.akunting.import_tb');
    Route::get('/import-manual-ap', 'Konfigurasi/MigrasiData@import_manual_ap')->name('migrasi_data.akunting.import_manual_ap');
    Route::get('/balance-ledger-tb', 'Konfigurasi/MigrasiData@balance_ledger_tb')->name('migrasi_data.akunting.balance_ledger_tb');
});
