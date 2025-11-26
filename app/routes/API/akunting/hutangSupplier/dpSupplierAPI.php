<?php

Route::group('api/akunting/hutang-supplier/dp-supplier', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/HutangSupplier/DpSupplierAPI@list')->name('api.akunting.hutang_supplier.dp_supplier');
    Route::get('/create', 'API/Akunting/HutangSupplier/DpSupplierAPI@form')->name('api.akunting.hutang_supplier.dp_supplier.create');
    Route::get('/edit', 'API/Akunting/HutangSupplier/DpSupplierAPI@form')->name('api.akunting.hutang_supplier.dp_supplier.edit');
    // Route::get('/template', 'API/Akunting/HutangSupplier/DpSupplierAPI@template')->name('api.akunting.hutang_supplier.dp_supplier.tpl');
    // Route::post('/{myid}/posting', 'API/Akunting/HutangSupplier/DpSupplierAPI@posting')->name('api.akunting.hutang_supplier.dp_supplier.posting');
    // Route::patch('/save/{mytype}', 'API/Akunting/HutangSupplier/DpSupplierAPI@save')->name('api.akunting.hutang_supplier.dp_supplier.save');
    // Route::post('/{myid}/delete', 'API/Akunting/HutangSupplier/DpSupplierAPI@delete')->name('api.akunting.hutang_supplier.dp_supplier.delete');
});