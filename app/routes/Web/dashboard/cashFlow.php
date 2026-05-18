<?php

Route::group('dashboard/cashflow', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Dashboard/CashFlow@list')->name('dashboard.cashflow');
});