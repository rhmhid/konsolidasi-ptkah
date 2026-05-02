<?php

Route::group('api/piutang-report/aging-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/PiutangReport/AgingPiutangAPI@excel')->name('api.piutang_report.aging_piutang.excel');
    Route::get('/detail/excel', 'API/AkuntansiReport/PiutangReport/AgingPiutangAPI@excel_detail')->name('api.piutang_report.aging_piutang.detail.excel');
});