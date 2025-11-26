<?php

Route::group('inventori/master-data/supplier', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/MasterData/Supplier@index')->name('inventori.master_data.supplier');
});