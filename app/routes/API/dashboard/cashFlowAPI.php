<?php

Route::group('api/dashboard/cashflow', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/data', 'API/Dashboard/CashFlowAPI@data')->name('api.dashboard.cashflow.data');
});