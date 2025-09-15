<?php

Route::group('api/akunting/piutang-pelanggan/manual-ar-payment', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@list')->name('api.akunting.piutang_pelanggan.manual_ar_payment');
    Route::get('/list-inv', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@list_inv')->name('api.akunting.piutang_pelanggan.manual_ar_payment.list_inv');
    Route::get('/create', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@form')->name('api.akunting.piutang_pelanggan.manual_ar_payment.create');
    Route::get('/edit', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@form')->name('api.akunting.piutang_pelanggan.manual_ar_payment.edit');
    Route::get('/cari-invoice', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@cari_invoice')->name('api.akunting.piutang_pelanggan.manual_ar_payment.cari_invoice');
    Route::patch('/save', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@save')->name('api.akunting.piutang_pelanggan.manual_ar_payment.save');
    Route::post('/{myid}/delete', 'API/Akunting/PiutangPelanggan/ManualArPaymentAPI@delete')->name('api.akunting.piutang_pelanggan.manual_ar_payment.delete');
});