<?php

Route::group('master-data/database/pelanggan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/Database/Pelanggan@index')->name('master_data.database.pelanggan');
});