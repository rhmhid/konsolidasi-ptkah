<?php

Route::group('api/master-data/database/kas-bank', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/MasterData/Database/KasbankAPI@list_data')->name('api.master_data.database.kas_bank');
    Route::get('/create', 'API/MasterData/Database/KasbankAPI@form')->name('api.master_data.database.kas_bank.create');
    Route::get('/edit', 'API/MasterData/Database/KasbankAPI@form')->name('api.master_data.database.kas_bank.edit');
    Route::post('/cek-kode/{kode}', 'API/MasterData/Database/KasbankAPI@cek_kode')->name('api.master_data.database.kas_bank.cek_kode');
    Route::patch('/save', 'API/MasterData/Database/KasbankAPI@save')->name('api.master_data.database.kas_bank.save');
});