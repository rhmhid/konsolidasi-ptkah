<?php

Route::group('master-data/database/kas-bank', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/Database/Kasbank@index')->name('master_data.database.kas_bank');
});