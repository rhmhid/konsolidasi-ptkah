<?php

Route::group('inventori/pemakaian-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/PemakaianBarang@list')->name('inventori.pemakaian_barang');
    Route::get('/{myid}/cetak', 'Inventori/PemakaianBarang@cetak')->name('inventori.pemakaian_barang.cetak');
});