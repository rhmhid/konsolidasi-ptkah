<?php

Route::group('piutang-report/kartu-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/PiutangReport/KartuPiutang@list')->name('piutang_report.kartu_piutang');
    Route::get('/cetak', 'AkuntansiReport/PiutangReport/KartuPiutang@cetak')->name('piutang_report.kartu_piutang.cetak');
});