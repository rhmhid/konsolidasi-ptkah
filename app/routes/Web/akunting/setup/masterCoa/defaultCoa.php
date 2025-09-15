<?php

Route::group('akunting/setup/master-coa/default-coa', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/MasterCoa/DefaultCoa@index')->name('akunting.setup.master_coa.default_coa');
});