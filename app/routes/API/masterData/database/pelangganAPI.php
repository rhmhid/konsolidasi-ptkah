<?php

Route::group('api/master-data/database/pelanggan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/MasterData/Database/PelangganAPI@list_data')->name('api.master_data.database.pelanggan');
    Route::get('/create', 'API/MasterData/Database/PelangganAPI@form')->name('api.master_data.database.pelanggan.create');
    Route::get('/edit', 'API/MasterData/Database/PelangganAPI@form')->name('api.master_data.database.pelanggan.edit');
    Route::post('/cek-kode/{kode}', 'API/MasterData/Database/PelangganAPI@cek_kode')->name('api.master_data.database.pelanggan.cek_kode');
    Route::patch('/save', 'API/MasterData/Database/PelangganAPI@save')->name('api.master_data.database.pelanggan.save');
});