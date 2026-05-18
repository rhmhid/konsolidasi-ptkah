<?php

Route::group('dashboard/overview', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Dashboard/Overview@list')->name('dashboard.overview');
});
