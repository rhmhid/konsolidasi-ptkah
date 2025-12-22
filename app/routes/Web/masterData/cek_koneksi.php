<?php

Route::group('master-data/cek_koneksi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/Cek_koneksi@index')->name('master_data.cek_koneksi');
});