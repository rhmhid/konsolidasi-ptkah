<?php

Route::group('inventori/distribusi-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/DistribusiBarang@list')->name('inventori.distribusi_barang');
    Route::get('/{myid}/cetak', 'Inventori/DistribusiBarang@cetak')->name('inventori.distribusi_barang.cetak');
});