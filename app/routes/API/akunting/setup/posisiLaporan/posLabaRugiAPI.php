<?php

Route::group('api/akunting/setup/posisi-laporan/pos-laba-rugi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@list_data')->name('api.akunting.setup.posisi_laporan.pos_pl');
    Route::get('/create', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@form')->name('api.akunting.setup.posisi_laporan.pos_pl.create');
    Route::get('/edit', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@form')->name('api.akunting.setup.posisi_laporan.pos_pl.edit');
    Route::patch('/save', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@save')->name('api.akunting.setup.posisi_laporan.pos_pl.save');
});

Route::group('api/akunting/setup/posisi-laporan/pos-laba-rugi-rekap', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@list_data_rekap')->name('api.akunting.setup.posisi_laporan.pos_pl.rekap');
    Route::get('/create', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@form_rekap')->name('api.akunting.setup.posisi_laporan.pos_pl.rekap.create');
    Route::get('/edit', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@form_rekap')->name('api.akunting.setup.posisi_laporan.pos_pl.rekap.edit');
    Route::patch('/save', 'API/Akunting/Setup/PosisiLaporan/PosplAPI@save_rekap')->name('api.akunting.setup.posisi_laporan.pos_pl.rekap.save');
});