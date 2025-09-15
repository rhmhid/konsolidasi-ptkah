<?php

Route::group('api/inventori/distribusi-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/DistribusiBarangAPI@list')->name('api.inventori.distribusi_barang');
    Route::get('/create', 'API/Inventori/DistribusiBarangAPI@create')->name('api.inventori.distribusi_barang.create');
    Route::get('/cari-barang', 'API/Inventori/DistribusiBarangAPI@cari_barang')->name('api.inventori.distribusi_barang.cari_barang');
    Route::patch('/save', 'API/Inventori/DistribusiBarangAPI@save')->name('api.inventori.distribusi_barang.save');
    Route::post('/{myid}/delete', 'API/Inventori/DistribusiBarangAPI@delete')->name('api.inventori.distribusi_barang.delete');
});