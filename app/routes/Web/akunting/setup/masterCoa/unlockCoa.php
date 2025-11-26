<?php

Route::group('akunting/setup/master-coa/unlock-coa', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/MasterCoa/UnlockCoa@index')->name('akunting.setup.master_coa.unlock_coa');
});