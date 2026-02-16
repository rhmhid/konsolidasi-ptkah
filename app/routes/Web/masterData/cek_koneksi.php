<?php

Route::group('master-data/connection-test', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/Cek_koneksi@index')->name('master_data.cek_koneksi');
    Route::get('/cobadb', 'MasterData/Cek_koneksi@cobadb')->name('master_data.cek_koneksi.cobadb');

});
