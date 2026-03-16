<?php

Route::group('dashboard/balancesheet', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Dashboard/Balancesheet@list')->name('dashboard.balancesheet');
});
