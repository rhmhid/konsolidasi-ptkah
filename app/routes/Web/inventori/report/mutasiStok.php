<?php

Route::group('inventori-report/mutasi-stok', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/Report/MutasiStok@list')->name('inventori_report.mutasi_stok');
    Route::get('/cetak', 'Inventori/Report/MutasiStok@cetak')->name('inventori_report.mutasi_stok.cetak');
});