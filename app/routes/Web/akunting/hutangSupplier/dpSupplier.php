<?php

Route::group('akunting/hutang-supplier/dp-supplier', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/HutangSupplier/DpSupplier@list')->name('akunting.hutang_supplier.dp_supplier');
});