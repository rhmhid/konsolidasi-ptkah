<?php

Route::group('inventori/master-data/merk', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/MasterData/Merk@index')->name('inventori.master_data.merk');
});