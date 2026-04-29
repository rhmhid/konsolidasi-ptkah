<?php

Route::group('summary-report/ar-company', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/SummaryReport/PiutangPenjamin@list')->name('summary_report.ar_company');
    Route::get('/cetak', 'AkuntansiReport/SummaryReport/PiutangPenjamin@cetak')->name('summary_report.ar_company.cetak');
    Route::get('detail', 'AkuntansiReport/SummaryReport/PiutangPenjamin@detail')->name('summary_report.ar_company.detail');
});