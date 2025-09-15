<?php

// Route::group('api/migrasi-data', ['middleware' => 'AuthMiddleware'], function ()
// {
//     Route::patch('/reset-data/save', 'API/Konfigurasi/MigrasiDataAPI@save_reset_data')->name('api.migrasi_data.reset_data.save');
//     Route::patch('/updol/pegawai/save', 'API/Konfigurasi/MigrasiDataAPI@save_updol_pegawai')->name('api.migrasi_data.updol.pegawai.save');
//     Route::get('/updol/user/list', 'API/Konfigurasi/MigrasiDataAPI@list_data_group_akses')->name('api.migrasi_data.updol.user.list');
//     Route::patch('/updol/user/save', 'API/Konfigurasi/MigrasiDataAPI@save_updol_user')->name('api.migrasi_data.updol.user.save');
// });

// Route::group('api/migrasi-data/akunting', ['middleware' => 'AuthMiddleware'], function ()
// {
//     // Serba Import TB
//     Route::get('/import-tb/download-file', 'API/Konfigurasi/MigrasiDataAPI@download_file_tb')->name('api.migrasi_data.akunting.import_tb.download');
//     Route::patch('/import-tb/save', 'API/Konfigurasi/MigrasiDataAPI@save_import_tb')->name('api.migrasi_data.akunting.import_tb.save');
//     Route::post('/import-tb/reset', 'API/Konfigurasi/MigrasiDataAPI@reset_tb')->name('api.migrasi_data.akunting.import_tb.reset');

//     // Balance Ledger TB
//     Route::patch('/balance-ledger-tb/save', 'API/Konfigurasi/MigrasiDataAPI@save_balance_ledger_tb')->name('api.migrasi_data.akunting.balance_ledger_tb.save');
// });