<?php

Route::group('dashboard/revenue', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Dashboard/Revenue@list')->name('dashboard.revenue');
});
