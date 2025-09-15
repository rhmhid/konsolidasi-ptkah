<?php

Route::group('api/inventori/master-data/satuan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/MasterData/SatuanAPI@list_data')->name('api.inventori.master_data.satuan');
    Route::get('/create', 'API/Inventori/MasterData/SatuanAPI@form')->name('api.inventori.master_data.satuan.create');
    Route::get('/edit', 'API/Inventori/MasterData/SatuanAPI@form')->name('api.inventori.master_data.satuan.edit');
    Route::post('/cek-kode/{kode}', 'API/Inventori/MasterData/SatuanAPI@cek_kode')->name('api.inventori.master_data.satuan.cek_kode');
    Route::patch('/save', 'API/Inventori/MasterData/SatuanAPI@save')->name('api.inventori.master_data.satuan.save');
});