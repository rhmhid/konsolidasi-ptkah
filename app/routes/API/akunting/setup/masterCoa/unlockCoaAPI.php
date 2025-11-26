<?php

Route::group('api/akunting/setup/master-coa/unlock-coa', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/MasterCoa/UnlockCoaAPI@list_data')->name('api.akunting.setup.master_coa.unlock_coa');
    Route::get('/edit', 'API/Akunting/Setup/MasterCoa/UnlockCoaAPI@edit')->name('api.akunting.setup.master_coa.unlock_coa.edit');
    Route::patch('/save', 'API/Akunting/Setup/MasterCoa/UnlockCoaAPI@save')->name('api.akunting.setup.master_coa.unlock_coa.save');
});