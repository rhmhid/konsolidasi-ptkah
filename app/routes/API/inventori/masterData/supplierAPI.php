<?php

Route::group('api/inventori/master-data/supplier', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/MasterData/SupplierAPI@list_data')->name('api.inventori.master_data.supplier');
    Route::get('/create', 'API/Inventori/MasterData/SupplierAPI@form')->name('api.inventori.master_data.supplier.create');
    Route::get('/edit', 'API/Inventori/MasterData/SupplierAPI@form')->name('api.inventori.master_data.supplier.edit');
    Route::post('/cek-kode/{kode}', 'API/Inventori/MasterData/SupplierAPI@cek_kode')->name('api.inventori.master_data.supplier.cek_kode');
    Route::patch('/save', 'API/Inventori/MasterData/SupplierAPI@save')->name('api.inventori.master_data.supplier.save');
});