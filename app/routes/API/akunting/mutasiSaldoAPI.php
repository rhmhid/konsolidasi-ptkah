<?php

Route::group('api/akunting/mutasi-saldo', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/MutasiSaldoAPI@list')->name('api.akunting.mutasi_saldo');
    Route::get('/create', 'API/Akunting/MutasiSaldoAPI@create')->name('api.akunting.mutasi_saldo.create');
    Route::post('/cek-saldo/{mybank}', 'API/Akunting/MutasiSaldoAPI@cek_saldo')->name('api.akunting.mutasi_saldo.cek_saldo');
    Route::patch('/save', 'API/Akunting/MutasiSaldoAPI@save')->name('api.akunting.mutasi_saldo.save');
    Route::post('/{myid}/delete', 'API/Akunting/MutasiSaldoAPI@delete')->name('api.akunting.mutasi_saldo.delete');
});