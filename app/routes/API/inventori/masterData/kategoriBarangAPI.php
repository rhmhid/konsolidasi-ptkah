<?php

Route::group('api/inventori/master-data/kategori-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/MasterData/KategoriBarangAPI@list_data')->name('api.inventori.master_data.kategori_barang');
    Route::get('/create', 'API/Inventori/MasterData/KategoriBarangAPI@form')->name('api.inventori.master_data.kategori_barang.create');
    Route::get('/edit', 'API/Inventori/MasterData/KategoriBarangAPI@form')->name('api.inventori.master_data.kategori_barang.edit');
    Route::post('/cek-kode/{kode}', 'API/Inventori/MasterData/KategoriBarangAPI@cek_kode')->name('api.inventori.master_data.kategori_barang.cek_kode');
    Route::patch('/save', 'API/Inventori/MasterData/KategoriBarangAPI@save')->name('api.inventori.master_data.kategori_barang.save');
});