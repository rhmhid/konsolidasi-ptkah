<?php

Route::group('akunting/mutasi-saldo', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/MutasiSaldo@list')->name('akunting.mutasi_saldo');
    Route::get('/{myid}/cetak', 'Akunting/MutasiSaldo@cetak')->name('akunting.mutasi_saldo.cetak');
});