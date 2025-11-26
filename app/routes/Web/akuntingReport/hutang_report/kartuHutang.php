<?php

Route::group('hutang-report/kartu-hutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/HutangReport/KartuHutang@list')->name('hutang_report.kartu_hutang');
    Route::get('/cetak', 'AkuntansiReport/HutangReport/KartuHutang@cetak')->name('hutang_report.kartu_hutang.cetak');
});