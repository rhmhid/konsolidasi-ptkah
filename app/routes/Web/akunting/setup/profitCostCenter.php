<?php

Route::group('akunting/setup/profit-cost-center', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/ProfitCostCenter@index')->name('akunting.setup.profit_cost_center');
});