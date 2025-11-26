<?php

Route::group('akuntansi-report/neraca-saldo', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/NeracaSaldo@list')->name('akuntansi_report.neraca_saldo');
    Route::get('/cetak', 'AkuntansiReport/NeracaSaldo@cetak')->name('akuntansi_report.neraca_saldo.cetak');
});