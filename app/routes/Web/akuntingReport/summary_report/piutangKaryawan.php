<?php

Route::group('summary-report/ar-karyawan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/SummaryReport/PiutangKaryawan@list')->name('summary_report.ar_emp');
    Route::get('/cetak', 'AkuntansiReport/SummaryReport/PiutangKaryawan@cetak')->name('summary_report.ar_emp.cetak');
    Route::get('detail', 'AkuntansiReport/SummaryReport/PiutangKaryawan@detail')->name('summary_report.ar_emp.detail');
});