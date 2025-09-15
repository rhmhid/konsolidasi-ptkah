<?php

Route::group('api/akuntansi-report/buku-besar', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/BukuBesarAPI@excel')->name('api.akuntansi_report.buku_besar.excel');
});