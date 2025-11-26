<?php

Route::group('api/master-data/pola-tarif/periode-tarif', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/MasterData/PolaTarif/PeriodeTarifAPI@list_data_period')->name('api.master_data.pola_tarif.periode_tarif');
    Route::get('/create', 'API/MasterData/PolaTarif/PeriodeTarifAPI@form')->name('api.master_data.pola_tarif.periode_tarif.create');
    Route::get('/edit', 'API/MasterData/PolaTarif/PeriodeTarifAPI@form')->name('api.master_data.pola_tarif.periode_tarif.edit');
    Route::post('/cek-kode/{kode}', 'API/MasterData/PolaTarif/PeriodeTarifAPI@cek_kode')->name('api.master_data.pola_tarif.periode_tarif.cek_kode');
    Route::patch('/save', 'API/MasterData/PolaTarif/PeriodeTarifAPI@save_period')->name('api.master_data.pola_tarif.periode_tarif.save');
});