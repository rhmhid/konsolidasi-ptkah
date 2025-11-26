<?php

Route::group('api/inventori/pemakaian-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/PemakaianBarangAPI@list')->name('api.inventori.pemakaian_barang');
    Route::get('/create', 'API/Inventori/PemakaianBarangAPI@create')->name('api.inventori.pemakaian_barang.create');
    Route::get('/cari-barang', 'API/Inventori/PemakaianBarangAPI@cari_barang')->name('api.inventori.pemakaian_barang.cari_barang');
    Route::patch('/save', 'API/Inventori/PemakaianBarangAPI@save')->name('api.inventori.pemakaian_barang.save');
    Route::post('/{myid}/delete', 'API/Inventori/PemakaianBarangAPI@delete')->name('api.inventori.pemakaian_barang.delete');
});