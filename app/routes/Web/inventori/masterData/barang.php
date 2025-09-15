<?php

Route::group('inventori/master-data/barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/MasterData/Barang@index')->name('inventori.master_data.barang');
});