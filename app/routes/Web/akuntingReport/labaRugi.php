<?php

Route::group('akuntansi-report/laba-rugi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/LabaRugi@list')->name('akuntansi_report.laba_rugi');
    Route::get('/{mytipe}/cetak', 'AkuntansiReport/LabaRugi@cetak')->name('akuntansi_report.laba_rugi.cetak');
    Route::get('/{mytipe}/{myid}/detail-coa/cetak', 'AkuntansiReport/LabaRugi@detail_coa')->name('akuntansi_report.laba_rugi.detail_coa.cetak');
});