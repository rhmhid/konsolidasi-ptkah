<?php

Route::group('master-data/database/cabang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/Database/Cabang@index')->name('master_data.database.cabang');
});