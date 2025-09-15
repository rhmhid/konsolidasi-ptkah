<?php

Route::group('api/akunting/setup/periode-akunting', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/PeriodeAkuntingAPI@list_data')->name('api.akunting.setup.periode_akunting');
    Route::get('/create', 'API/Akunting/Setup/PeriodeAkuntingAPI@create')->name('api.akunting.setup.periode_akunting.create');
    Route::patch('/save', 'API/Akunting/Setup/PeriodeAkuntingAPI@save')->name('api.akunting.setup.periode_akunting.save');
});