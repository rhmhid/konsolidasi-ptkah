<?php

Route::group('api/summary-report/ar-karyawan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/SummaryReport/PiutangKaryawanAPI@excel')->name('api.summary_report.ar_emp.excel');
    Route::get('/detail/excel', 'API/AkuntansiReport/SummaryReport/PiutangKaryawanAPI@excel_detail')->name('api.summary_report.ar_emp.detail.excel');
});