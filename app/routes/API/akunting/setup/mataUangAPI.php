<?php

Route::group('api/akunting/setup/mata-uang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/MataUangAPI@list_data')->name('api.akunting.setup.mata_uang');
    Route::get('/create', 'API/Akunting/Setup/MataUangAPI@create')->name('api.akunting.setup.mata_uang.create');
    Route::get('/edit', 'API/Akunting/Setup/MataUangAPI@edit')->name('api.akunting.setup.mata_uang.edit');
    Route::post('/cek-kode/{kode}', 'API/Akunting/Setup/MataUangAPI@cek_kode')->name('api.akunting.setup.mata_uang.cek_kode');
    Route::patch('/save', 'API/Akunting/Setup/MataUangAPI@save')->name('api.akunting.setup.mata_uang.save');
    Route::get('/rates', 'API/Akunting/Setup/MataUangAPI@rates')->name('api.akunting.setup.mata_uang.rates');
    Route::get('/rates-list', 'API/Akunting/Setup/MataUangAPI@list_data_rates')->name('api.akunting.setup.mata_uang.rates.list');
    Route::patch('/rates-save', 'API/Akunting/Setup/MataUangAPI@save_rates')->name('api.akunting.setup.mata_uang.rates.save');
});