<?php

Route::group('api/akunting/setup/posisi-laporan/pos-laba-rugi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@list_data')->name('api.akunting.setup.posisi_laporan.pos_pl');
    Route::get('/create', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@form')->name('api.akunting.setup.posisi_laporan.pos_pl.create');
    Route::get('/edit', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@form')->name('api.akunting.setup.posisi_laporan.pos_pl.edit');
    Route::patch('/save', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@save')->name('api.akunting.setup.posisi_laporan.pos_pl.save');
});