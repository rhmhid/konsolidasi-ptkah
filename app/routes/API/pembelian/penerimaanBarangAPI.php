<?php

Route::group('api/pembelian/penerimaan-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Pembelian/PenerimaanBarangAPI@list')->name('api.pembelian.penerimaan_barang');
    Route::get('/create', 'API/Pembelian/PenerimaanBarangAPI@form')->name('api.pembelian.penerimaan_barang.create');
    Route::get('/edit', 'API/Pembelian/PenerimaanBarangAPI@form')->name('api.pembelian.penerimaan_barang.edit');
    Route::get('/check-barang', 'API/Pembelian/PenerimaanBarangAPI@cari_barang')->name('api.pembelian.penerimaan_barang.cari_barang');
    Route::patch('/save', 'API/Pembelian/PenerimaanBarangAPI@save')->name('api.pembelian.penerimaan_barang.save');
    Route::post('/{myid}/delete', 'API/Pembelian/PenerimaanBarangAPI@delete')->name('api.pembelian.penerimaan_barang.delete');
});