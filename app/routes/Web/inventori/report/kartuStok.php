<?php

Route::group('inventori-report/kartu-stok', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/Report/KartuStok@list')->name('inventori_report.kartu_stok');
    Route::get('/cetak', 'Inventori/Report/KartuStok@cetak')->name('inventori_report.kartu_stok.cetak');
});