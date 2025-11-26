<?php

Route::group('api/akunting/hutang-supplier/manual-ap-payment', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@list')->name('api.akunting.hutang_supplier.manual_ap_payment');
    Route::get('/list-inv', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@list_inv')->name('api.akunting.hutang_supplier.manual_ap_payment.list_inv');
    Route::get('/create', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@form')->name('api.akunting.hutang_supplier.manual_ap_payment.create');
    Route::get('/edit', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@form')->name('api.akunting.hutang_supplier.manual_ap_payment.edit');
    Route::get('/cari-invoice', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@cari_invoice')->name('api.akunting.hutang_supplier.manual_ap_payment.cari_invoice');
    Route::patch('/save', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@save')->name('api.akunting.hutang_supplier.manual_ap_payment.save');
    Route::post('/{myid}/delete', 'API/Akunting/HutangSupplier/ManualApPaymentAPI@delete')->name('api.akunting.hutang_supplier.manual_ap_payment.delete');
});