<?php

Route::group('api/inventori/setup/barang-supplier', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/Setup/BarangSupplierAPI@list_data')->name('api.inventori.setup.barang_supplier');
    Route::get('/create', 'API/Inventori/Setup/BarangSupplierAPI@form')->name('api.inventori.setup.barang_supplier.create');
    Route::get('/edit', 'API/Inventori/Setup/BarangSupplierAPI@form')->name('api.inventori.setup.barang_supplier.edit');
    Route::get('/cari-barang', 'API/Inventori/Setup/BarangSupplierAPI@cari_barang')->name('api.inventori.setup.barang_supplier.cari_barang');
    Route::patch('/save', 'API/Inventori/Setup/BarangSupplierAPI@save')->name('api.inventori.setup.barang_supplier.save');
    Route::post('/{myid}/delete', 'API/Inventori/Setup/BarangSupplierAPI@delete')->name('api.inventori.setup.barang_supplier.delete');
});