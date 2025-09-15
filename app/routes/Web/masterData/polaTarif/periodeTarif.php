<?php

Route::group('master-data/pola-tarif/periode-tarif', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'MasterData/PolaTarif/PeriodeTarif@index')->name('master_data.pola_tarif.periode_tarif');
});