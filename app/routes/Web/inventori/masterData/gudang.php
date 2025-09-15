<?php

Route::group('inventori/master-data/gudang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/MasterData/Gudang@index')->name('inventori.master_data.gudang');
});