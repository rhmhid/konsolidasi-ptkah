<?php

Route::group('api/akunting/hutang-supplier/manual-ap', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/HutangSupplier/ManualApAPI@list')->name('api.akunting.hutang_supplier.manual_ap');
    Route::get('/create', 'API/Akunting/HutangSupplier/ManualApAPI@form')->name('api.akunting.hutang_supplier.manual_ap.create');
    Route::get('/edit', 'API/Akunting/HutangSupplier/ManualApAPI@form')->name('api.akunting.hutang_supplier.manual_ap.edit');
    Route::patch('/save', 'API/Akunting/HutangSupplier/ManualApAPI@save')->name('api.akunting.hutang_supplier.manual_ap.save');
    Route::post('/{myid}/delete', 'API/Akunting/HutangSupplier/ManualApAPI@delete')->name('api.akunting.hutang_supplier.manual_ap.delete');
});