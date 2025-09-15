<?php

Route::group('api/akunting/piutang-pelanggan/manual-ar', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/PiutangPelanggan/ManualArAPI@list')->name('api.akunting.piutang_pelanggan.manual_ar');
    Route::get('/create', 'API/Akunting/PiutangPelanggan/ManualArAPI@form')->name('api.akunting.piutang_pelanggan.manual_ar.create');
    Route::get('/edit', 'API/Akunting/PiutangPelanggan/ManualArAPI@form')->name('api.akunting.piutang_pelanggan.manual_ar.edit');
    Route::patch('/save/{mytype}', 'API/Akunting/PiutangPelanggan/ManualArAPI@save')->name('api.akunting.piutang_pelanggan.manual_ar.save');
    Route::post('/{myid}/delete', 'API/Akunting/PiutangPelanggan/ManualArAPI@delete')->name('api.akunting.piutang_pelanggan.manual_ar.delete');
    Route::get('/template', 'API/Akunting/PiutangPelanggan/ManualArAPI@template')->name('api.akunting.piutang_pelanggan.manual_ar.tpl');
});