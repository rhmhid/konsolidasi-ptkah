<?php

Route::group('akunting/setup/mata-uang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/MataUang@index')->name('akunting.setup.mata_uang');
});