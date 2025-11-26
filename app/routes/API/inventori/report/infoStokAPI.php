<?php

Route::group('api/inventori-report/info-stok', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/Inventori/Report/InfoStokAPI@excel')->name('api.inventori_report.info_stok.excel');
});