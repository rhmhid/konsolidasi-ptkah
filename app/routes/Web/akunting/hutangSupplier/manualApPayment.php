<?php

Route::group('akunting/hutang-supplier/manual-ap-payment', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/HutangSupplier/ManualApPayment@list')->name('akunting.hutang_supplier.manual_ap_payment');
    Route::post('/data-invoice', 'Akunting/HutangSupplier/ManualApPayment@data_invoice')->name('api.akunting.hutang_supplier.manual_ap_payment.data_invoice');
    Route::get('/{myid}/cetak', 'Akunting/HutangSupplier/ManualApPayment@cetak')->name('akunting.hutang_supplier.manual_ap_payment.cetak');
});