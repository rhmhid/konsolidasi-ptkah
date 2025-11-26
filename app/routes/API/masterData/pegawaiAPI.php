<?php

Route::group('api/master-data/pegawai', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/MasterData/PegawaiAPI@list_data')->name('api.master_data.pegawai');
    Route::get('/create', 'API/MasterData/PegawaiAPI@form')->name('api.master_data.pegawai.create');
    Route::get('/edit', 'API/MasterData/PegawaiAPI@form')->name('api.master_data.pegawai.edit');
    Route::post('/cek-kode/{kode}', 'API/MasterData/PegawaiAPI@cek_kode')->name('api.master_data.pegawai.cek_kode');
    Route::patch('/save', 'API/MasterData/PegawaiAPI@save')->name('api.master_data.pegawai.save');
});