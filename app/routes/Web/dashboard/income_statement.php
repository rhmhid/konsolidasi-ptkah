<?php

Route::group('dashboard/income-statement', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Dashboard/IncomeStatement@list')->name('dashboard.income_statement');
});
