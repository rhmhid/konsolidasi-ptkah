<?php

Route::group('akunting/piutang-pelanggan/manual-ar', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/PiutangPelanggan/ManualAr@list')->name('akunting.piutang_pelanggan.manual_ar');
    Route::get('/{myid}/cetak', 'Akunting/PiutangPelanggan/ManualAr@cetak')->name('akunting.piutang_pelanggan.manual_ar.cetak');
});