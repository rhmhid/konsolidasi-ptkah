<?php

Route::group('api/akunting/setup/posisi-laporan/pos-neraca', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/PosisiLaporan/PosNeracaAPI@list_data')->name('api.akunting.setup.posisi_laporan.pos_neraca');
    Route::get('/create', 'API/Akunting/Setup/PosisiLaporan/PosNeracaAPI@form')->name('api.akunting.setup.posisi_laporan.pos_neraca.create');
    Route::get('/edit', 'API/Akunting/Setup/PosisiLaporan/PosNeracaAPI@form')->name('api.akunting.setup.posisi_laporan.pos_neraca.edit');
    Route::patch('/save', 'API/Akunting/Setup/PosisiLaporan/PosNeracaAPI@save')->name('api.akunting.setup.posisi_laporan.pos_neraca.save');
});