<?php

Route::group('api/keuangan-report/kas-bank', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/KeuanganReport/KasBankAPI@excel')->name('api.keuangan_report.kas_bank.excel');
});