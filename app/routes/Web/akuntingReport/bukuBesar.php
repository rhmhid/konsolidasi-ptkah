<?php

Route::group('akuntansi-report/buku-besar', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/BukuBesar@list')->name('akuntansi_report.buku_besar');
    Route::get('/cetak', 'AkuntansiReport/BukuBesar@cetak')->name('akuntansi_report.buku_besar.cetak');
});