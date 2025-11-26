<?php

Route::group('api/akunting/hutang-supplier/pembayaran-invoice', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@list')->name('api.akunting.hutang_supplier.pembayaran_invoice');
    Route::get('/create', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@form')->name('api.akunting.hutang_supplier.pembayaran_invoice.create');
    Route::get('/edit', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@form')->name('api.akunting.hutang_supplier.pembayaran_invoice.edit');
    Route::get('/{myid}/info-supplier', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@info_supplier')->name('api.akunting.hutang_supplier.pembayaran_invoice.info_supplier');
    Route::get('/{myid}/list-outstanding-ap', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@list_outstanding_ap')->name('api.akunting.hutang_supplier.pembayaran_invoice.list_outstanding_ap');
    Route::patch('/save', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@save')->name('api.akunting.hutang_supplier.pembayaran_invoice.save');
    Route::post('/{myid}/delete', 'API/Akunting/HutangSupplier/PembayaranInvoiceAPI@delete')->name('api.akunting.hutang_supplier.pembayaran_invoice.delete');
});