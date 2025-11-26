<?php

Route::group('api/master-data/pola-tarif/group-tarif', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/MasterData/PolaTarif/GroupTarifAPI@list_data')->name('api.master_data.pola_tarif.group_tarif');
    Route::get('/create', 'API/MasterData/PolaTarif/GroupTarifAPI@form')->name('api.master_data.pola_tarif.group_tarif.create');
    Route::get('/edit', 'API/MasterData/PolaTarif/GroupTarifAPI@form')->name('api.master_data.pola_tarif.group_tarif.edit');
    Route::post('/cek-kode/{kode}', 'API/MasterData/PolaTarif/GroupTarifAPI@cek_kode')->name('api.master_data.pola_tarif.group_tarif.cek_kode');
    Route::patch('/save', 'API/MasterData/PolaTarif/GroupTarifAPI@save')->name('api.master_data.pola_tarif.group_tarif.save');
});