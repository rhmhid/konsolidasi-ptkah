<?php

Route::group('keuangan-report/kas-bank', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/KeuanganReport/KasBank@list')->name('keuangan_report.kas_bank');
    Route::get('/cetak', 'AkuntansiReport/KeuanganReport/KasBank@cetak')->name('keuangan_report.kas_bank.cetak');
});