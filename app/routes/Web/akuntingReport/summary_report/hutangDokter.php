<?php

Route::group('summary-report/ap-dokter', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/SummaryReport/HutangDokter@list')->name('summary_report.ap_dokter');
    Route::get('/cetak', 'AkuntansiReport/SummaryReport/HutangDokter@cetak')->name('summary_report.ap_dokter.cetak');
    Route::get('detail', 'AkuntansiReport/SummaryReport/HutangDokter@detail')->name('summary_report.ap_dokter.detail');
});