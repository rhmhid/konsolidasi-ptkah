<?php

Route::group('master-data/pola-tarif/group-tarif', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/PolaTarif/GroupTarif@index')->name('master_data.pola_tarif.group_tarif');
});