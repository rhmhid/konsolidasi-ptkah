<?php

Route::group('inventori/setup/barang-supplier', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/Setup/BarangSupplier@index')->name('inventori.setup.barang_supplier');
});