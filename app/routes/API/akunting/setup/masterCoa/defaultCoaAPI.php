<?php

Route::group('api/akunting/setup/master-coa/default-coa', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/MasterCoa/DefaultCoaAPI@list_data')->name('api.akunting.setup.master_coa.default_coa');
    Route::get('/edit', 'API/Akunting/Setup/MasterCoa/DefaultCoaAPI@edit')->name('api.akunting.setup.master_coa.default_coa.edit');
    Route::patch('/update', 'API/Akunting/Setup/MasterCoa/DefaultCoaAPI@update')->name('api.akunting.setup.master_coa.default_coa.update');
});