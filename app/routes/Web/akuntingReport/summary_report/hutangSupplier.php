<?php

Route::group('summary-report/ap-purchasing', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/SummaryReport/HutangSupplier@list')->name('summary_report.ap_purchasing');
    Route::get('/cetak', 'AkuntansiReport/SummaryReport/HutangSupplier@cetak')->name('summary_report.ap_purchasing.cetak');
});