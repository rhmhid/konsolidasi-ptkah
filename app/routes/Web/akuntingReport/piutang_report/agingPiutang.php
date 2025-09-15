<?php

Route::group('piutang-report/aging-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/PiutangReport/AgingPiutang@list')->name('piutang_report.aging_piutang');
    Route::get('/cetak', 'AkuntansiReport/PiutangReport/AgingPiutang@cetak')->name('piutang_report.aging_piutang.cetak');
});