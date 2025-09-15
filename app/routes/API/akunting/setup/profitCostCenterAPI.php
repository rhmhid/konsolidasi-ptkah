<?php

Route::group('api/akunting/setup/profit-cost-center', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/ProfitCostCenterAPI@list_data')->name('api.akunting.setup.profit_cost_center');
    Route::get('/create', 'API/Akunting/Setup/ProfitCostCenterAPI@create')->name('api.akunting.setup.profit_cost_center.create');
    Route::get('/edit', 'API/Akunting/Setup/ProfitCostCenterAPI@edit')->name('api.akunting.setup.profit_cost_center.edit');
    Route::post('/cek-kode/{kode}', 'API/Akunting/Setup/ProfitCostCenterAPI@cek_kode')->name('api.akunting.setup.profit_cost_center.cek_kode');
    Route::patch('/save', 'API/Akunting/Setup/ProfitCostCenterAPI@save')->name('api.akunting.setup.profit_cost_center.save');
});