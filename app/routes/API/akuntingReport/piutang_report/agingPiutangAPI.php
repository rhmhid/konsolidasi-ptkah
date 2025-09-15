<?php

Route::group('api/piutang-report/aging-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/PiutangReport/AgingPiutangAPI@excel')->name('api.hutang_report.aging_piutang.excel');
});