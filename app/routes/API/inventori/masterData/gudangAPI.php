<?php

Route::group('api/inventori/master-data/gudang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/MasterData/GudangAPI@list_data')->name('api.inventori.master_data.gudang');
    Route::get('/create', 'API/Inventori/MasterData/GudangAPI@form')->name('api.inventori.master_data.gudang.create');
    Route::get('/edit', 'API/Inventori/MasterData/GudangAPI@form')->name('api.inventori.master_data.gudang.edit');
    Route::post('/cek-kode', 'API/Inventori/MasterData/GudangAPI@cek_kode')->name('api.inventori.master_data.gudang.cek_kode');
    Route::patch('/save', 'API/Inventori/MasterData/GudangAPI@save')->name('api.inventori.master_data.gudang.save');
});