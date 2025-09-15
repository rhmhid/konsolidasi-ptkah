<?php

Route::group('api/inventori-report/mutasi-stok', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/Inventori/Report/MutasiStokAPI@excel')->name('api.inventori_report.mutasi_stok.excel');
});