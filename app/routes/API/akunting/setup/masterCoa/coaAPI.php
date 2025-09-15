<?php

Route::group('api/akunting/setup/master-coa/coa', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/MasterCoa/CoaAPI@list_data')->name('api.akunting.setup.master_coa.coa');
    Route::get('/create', 'API/Akunting/Setup/MasterCoa/CoaAPI@create')->name('api.akunting.setup.master_coa.coa.create');
    Route::get('/edit', 'API/Akunting/Setup/MasterCoa/CoaAPI@edit')->name('api.akunting.setup.master_coa.coa.edit');
    Route::patch('/save', 'API/Akunting/Setup/MasterCoa/CoaAPI@save')->name('api.akunting.setup.master_coa.coa.save');
});