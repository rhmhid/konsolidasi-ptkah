<?php

Route::group('inventori/master-data/kategori-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/MasterData/KategoriBarang@index')->name('inventori.master_data.kategori_barang');
});