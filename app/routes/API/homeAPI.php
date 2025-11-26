<?php

Route::group('api/home', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::post('/list-data', 'API/HomeAPI@list_data')->name('api.home');
});