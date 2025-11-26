<?php

Route::group('inventori-report/info-stok', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/Report/InfoStok@list')->name('inventori_report.info_stok');
    Route::get('/cetak', 'Inventori/Report/InfoStok@cetak')->name('inventori_report.info_stok.cetak');
    Route::get('/detail-stok', 'Inventori/Report/InfoStok@detail_stok')->name('inventori_report.info_stok.detail_stok');
});