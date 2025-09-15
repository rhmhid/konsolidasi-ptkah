<?php

Route::group('api/master-data/database/cabang/{type}', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'API/MasterData/Database/CabangAPI@list')->name('api.master_data.database.cabang');
    Route::get('/list-data', 'API/MasterData/Database/CabangAPI@list_data')->name('api.master_data.database.cabang.list');
    Route::get('/create', 'API/MasterData/Database/CabangAPI@form')->name('api.master_data.database.cabang.create');
    Route::get('/edit', 'API/MasterData/Database/CabangAPI@form')->name('api.master_data.database.cabang.edit');
    Route::post('/cek-kode/{kode}', 'API/MasterData/Database/CabangAPI@cek_kode')->name('api.master_data.database.cabang.cek_kode');
    Route::patch('/save', 'API/MasterData/Database/CabangAPI@save')->name('api.master_data.database.cabang.save');
});

Route::get('api/cabang/assign-branch', 'API/MasterData/Database/CabangAPI@assign_branch')->name('api.master_data.database.cabang.assign_branch');
Route::patch('api/cabang/assign-branch/save', 'API/MasterData/Database/CabangAPI@save_assign_branch')->name('api.master_data.database.cabang.assign_branch.save');