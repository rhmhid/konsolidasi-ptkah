<?php

Route::group('api/summary-report/ar-company', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/SummaryReport/PiutangPenjaminAPI@excel')->name('api.summary_report.ar_company.excel');
    Route::get('/detail/excel', 'API/AkuntansiReport/SummaryReport/PiutangPenjaminAPI@excel_detail')->name('api.summary_report.ar_company.detail.excel');
});