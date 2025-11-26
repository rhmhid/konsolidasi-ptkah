<?php

Route::group('akunting/daftar-jurnal', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/DaftarJurnal@list')->name('akunting.daftar_jurnal');
    Route::get('/{myglid}/cetak', 'Akunting/DaftarJurnal@cetak')->name('akunting.daftar_jurnal.cetak');
});