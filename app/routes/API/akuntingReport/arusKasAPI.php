<?php

Route::group('api/akuntansi-report/arus-kas', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/{mytipe}/excel', 'API/AkuntansiReport/ArusKasAPI@excel')->name('api.akuntansi_report.arus_kas.excel');
});