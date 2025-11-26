<?php

Route::group('akunting/setup/master-coa/coa', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/MasterCoa/Coa@index')->name('akunting.setup.master_coa.coa');
});