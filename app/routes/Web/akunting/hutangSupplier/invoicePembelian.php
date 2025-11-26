<?php

Route::group('akunting/hutang-supplier/invoice-pembelian', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/HutangSupplier/InvoicePembelian@list')->name('akunting.hutang_supplier.invoice_pembelian');
    Route::get('/{myid}/cetak-voucher', 'Akunting/HutangSupplier/InvoicePembelian@cetak_voucher')->name('akunting.hutang_supplier.invoice_pembelian.cetak_voucher');
    Route::get('/{myid}/cetak-faktur', 'Akunting/HutangSupplier/InvoicePembelian@cetak_faktur')->name('akunting.hutang_supplier.invoice_pembelian.cetak_faktur');
});