<?php

Route::group('akunting/piutang-pelanggan/manual-ar-payment', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/PiutangPelanggan/ManualArPayment@list')->name('akunting.piutang_pelanggan.manual_ar_payment');
    Route::post('/data-invoice', 'Akunting/PiutangPelanggan/ManualArPayment@data_invoice')->name('api.akunting.piutang_pelanggan.manual_ar_payment.data_invoice');
    Route::get('/{myid}/cetak', 'Akunting/PiutangPelanggan/ManualArPayment@cetak')->name('akunting.piutang_pelanggan.manual_ar_payment.cetak');
});


