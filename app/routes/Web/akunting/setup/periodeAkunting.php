<?php

Route::group('akunting/setup/periode-akunting', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/PeriodeAkunting@index')->name('akunting.setup.periode_akunting');
});