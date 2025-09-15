<?php

Route::group('api/piutang-report/kartu-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/PiutangReport/KartuPiutangAPI@excel')->name('api.piutang_report.kartu_piutang.excel');
});