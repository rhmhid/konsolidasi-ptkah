<?php

Route::group('akuntansi-report/neraca', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/Neraca@list')->name('akuntansi_report.neraca');
    Route::get('/{mytipe}/cetak', 'AkuntansiReport/Neraca@cetak')->name('akuntansi_report.neraca.cetak');
});