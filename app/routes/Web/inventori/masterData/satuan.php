<?php

Route::group('inventori/master-data/satuan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/MasterData/Satuan@index')->name('inventori.master_data.satuan');
});