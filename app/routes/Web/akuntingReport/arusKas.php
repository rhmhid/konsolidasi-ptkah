<?php

Route::group('akuntansi-report/arus-kas', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/ArusKas@list')->name('akuntansi_report.arus_kas');
    Route::get('/{mytipe}/cetak', 'AkuntansiReport/ArusKas@cetak')->name('akuntansi_report.arus_kas.cetak');
    Route::get('/{mytipe}/cetak/{myid}/detail', 'AkuntansiReport/ArusKas@direct_coa')->name('akuntansi_report.arus_kas.cetak.detail');
});