<?php

Route::group('api/inventori-report/kartu-stok', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/Inventori/Report/KartuStokAPI@excel')->name('api.inventori_report.kartu_stok.excel');
});