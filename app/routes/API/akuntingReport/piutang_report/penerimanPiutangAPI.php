<?php

Route::group('api/piutang-report/penerimaan-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/PiutangReport/PenerimaanPiutangAPI@excel')->name('api.piutang_report.penerimaan_piutang.excel');
});