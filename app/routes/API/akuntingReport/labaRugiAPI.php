<?php

Route::group('api/akuntansi-report/laba-rugi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/{mytipe}/excel', 'API/AkuntansiReport/LabaRugiAPI@excel')->name('api.akuntansi_report.laba_rugi.excel');
});