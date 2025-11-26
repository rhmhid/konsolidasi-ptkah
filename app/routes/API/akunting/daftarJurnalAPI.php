<?php

Route::group('api/akunting/daftar-jurnal', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/DaftarJurnalAPI@list')->name('api.akunting.daftar_jurnal');
    Route::get('/{myglid}/detail', 'API/Akunting/DaftarJurnalAPI@detail')->name('api.akunting.daftar_jurnal.detail');
});