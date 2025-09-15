<?php

Route::group('hutang-report/aging-hutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/HutangReport/AgingHutang@list')->name('hutang_report.aging_hutang');
    Route::get('/cetak', 'AkuntansiReport/HutangReport/AgingHutang@cetak')->name('hutang_report.aging_hutang.cetak');
});