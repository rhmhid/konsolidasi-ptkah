<?php

Route::group('akunting/hutang-supplier/manual-ap', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/HutangSupplier/ManualAp@list')->name('akunting.hutang_supplier.manual_ap');
    Route::get('/{myid}/cetak', 'Akunting/HutangSupplier/ManualAp@cetak')->name('akunting.hutang_supplier.manual_ap.cetak');
});