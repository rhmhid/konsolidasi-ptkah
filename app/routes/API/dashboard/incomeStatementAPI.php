<?php

Route::group('api/dashboard/income-statement', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/data', 'API/Dashboard/IncomeStatementAPI@data')->name('api.dashboard.income_statement.data');
});