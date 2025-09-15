<?php

Route::group('master-data/pegawai', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/Pegawai@index')->name('master_data.pegawai');
});