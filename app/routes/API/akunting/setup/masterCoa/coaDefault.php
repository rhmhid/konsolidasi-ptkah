<?php

Route::group('api/akunting/setup/master-coa/{sctype}/coa-default', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'API/Akunting/Setup/MasterCoa/CoaDefaultAPI@index')->name('api.akunting.setup.master_coa.coa_default');
    Route::get('/list-data', 'API/Akunting/Setup/MasterCoa/CoaDefaultAPI@list_data')->name('api.akunting.setup.master_coa.coa_default.list');
    Route::post('/update', 'API/Akunting/Setup/MasterCoa/CoaDefaultAPI@update')->name('api.akunting.setup.master_coa.coa_default.update');
    Route::patch('/save', 'API/Akunting/Setup/MasterCoa/CoaDefaultAPI@save')->name('api.akunting.setup.master_coa.coa_default.save');
});