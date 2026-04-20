<?php

Route::group('api/summary-report/ap-purchasing', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/SummaryReport/HutangSupplierAPI@excel')->name('api.summary_report.ap_purchasing.excel');
    Route::get('/detail/excel', 'API/AkuntansiReport/SummaryReport/HutangSupplierAPI@excel_detail')->name('api.summary_report.ap_purchasing.detail.excel');
});