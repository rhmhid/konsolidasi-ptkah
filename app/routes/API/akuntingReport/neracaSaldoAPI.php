<?php

Route::group('api/akuntansi-report/neraca-saldo', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/NeracaSaldoAPI@excel')->name('api.akuntansi_report.neraca_saldo.excel');
});