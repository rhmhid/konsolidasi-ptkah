<?php

Route::group('hutang-report/aging-hutang-unbill', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/HutangReport/AgingHutangUnbill@list')->name('hutang_report.aging_hutang_unbill');
    Route::get('/cetak', 'AkuntansiReport/HutangReport/AgingHutangUnbill@cetak')->name('hutang_report.aging_hutang_unbill.cetak');
    Route::get('detail', 'AkuntansiReport/HutangReport/AgingHutangUnbill@detail')->name('hutang_report.aging_hutang_unbill.detail');
});