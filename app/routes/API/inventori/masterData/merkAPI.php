<?php

Route::group('api/inventori/master-data/merk', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/MasterData/MerkAPI@list_data')->name('api.inventori.master_data.merk');
    Route::get('create', 'API/Inventori/MasterData/MerkAPI@form')->name('api.inventori.master_data.merk.create');
    Route::get('/edit', 'API/Inventori/MasterData/MerkAPI@form')->name('api.inventori.master_data.merk.edit');
    Route::post('/cek-kode/{kode}', 'API/Inventori/MasterData/MerkAPI@cek_kode')->name('api.inventori.master_data.merk.cek_kode');
    Route::patch('/save', 'API/Inventori/MasterData/MerkAPI@save')->name('api.inventori.master_data.merk.save');
});