<?php

Route::group('pembelian-report/penerimaan-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Pembelian/Report/PenerimaanBarang@list')->name('pembelian_report.penerimaan_barang');
    Route::get('/cetak', 'Pembelian/Report/PenerimaanBarang@cetak')->name('pembelian_report.penerimaan_barang.cetak');
});