<?php

Route::group('api/summary-report/ap-dokter', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/SummaryReport/HutangDokterAPI@excel')->name('api.summary_report.ap_dokter.excel');
    Route::get('/detail/excel', 'API/AkuntansiReport/SummaryReport/HutangDokterAPI@excel_detail')->name('api.summary_report.ap_dokter.detail.excel');
});