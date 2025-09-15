<?php

Route::group('api/akunting/hutang-supplier/invoice-pembelian', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@list')->name('api.akunting.hutang_supplier.invoice_pembelian');
    Route::get('/create', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@form')->name('api.akunting.hutang_supplier.invoice_pembelian.create');
    Route::get('/edit', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@form')->name('api.akunting.hutang_supplier.invoice_pembelian.edit');
    Route::get('/popup-penerimaan', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@popup_penerimaan')->name('api.akunting.hutang_supplier.invoice_pembelian.popup_penerimaan');
    Route::get('/list-data-penerimaan', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@list_penerimaan')->name('api.akunting.hutang_supplier.invoice_pembelian.list_penerimaan');
    Route::patch('/save', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@save')->name('api.akunting.hutang_supplier.invoice_pembelian.save');
    Route::post('/{myid}/delete', 'API/Akunting/HutangSupplier/InvoicePembelianAPI@delete')->name('api.akunting.hutang_supplier.invoice_pembelian.delete');
});