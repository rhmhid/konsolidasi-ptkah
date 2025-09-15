<?php

Route::group('api/inventori/master-data/barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/MasterData/BarangAPI@list_data')->name('api.inventori.master_data.barang');
    Route::get('/create', 'API/Inventori/MasterData/BarangAPI@form')->name('api.inventori.master_data.barang.create');
    Route::get('/edit', 'API/Inventori/MasterData/BarangAPI@form')->name('api.inventori.master_data.barang.edit');
    Route::post('/cek-kode/{kode}', 'API/Inventori/MasterData/BarangAPI@cek_kode')->name('api.inventori.master_data.barang.cek_kode');
    Route::get('barang-satuan', 'API/Inventori/MasterData/BarangAPI@barang_satuan')->name('api.inventori.master_data.barang.barang_satuan');
    Route::patch('/save', 'API/Inventori/MasterData/BarangAPI@save')->name('api.inventori.master_data.barang.save');
});