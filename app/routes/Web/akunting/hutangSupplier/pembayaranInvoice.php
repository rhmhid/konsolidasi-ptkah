<?php

Route::group('akunting/hutang-supplier/pembayaran-invoice', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/HutangSupplier/PembayaranInvoice@list')->name('akunting.hutang_supplier.pembayaran_invoice');
    Route::get('/{myid}/cetak-bukti', 'Akunting/HutangSupplier/PembayaranInvoice@cetak_bukti')->name('akunting.hutang_supplier.pembayaran_invoice.cetak_bukti');
});