<?php

Route::group('api/akuntansi-report/neraca', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/{mytipe}/excel', 'API/AkuntansiReport/NeracaAPI@excel')->name('api.akuntansi_report.neraca.excel');
});