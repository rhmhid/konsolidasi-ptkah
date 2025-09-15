<?php

Route::group('api/akunting/setup/posisi-laporan/pos-arus-kas', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/Setup/PosisiLaporan/PosArusKasAPI@list_data')->name('api.akunting.setup.posisi_laporan.pos_arus_kas');
    Route::get('/create', 'API/Akunting/Setup/PosisiLaporan/PosArusKasAPI@form')->name('api.akunting.setup.posisi_laporan.pos_arus_kas.create');
    Route::get('/edit', 'API/Akunting/Setup/PosisiLaporan/PosArusKasAPI@form')->name('api.akunting.setup.posisi_laporan.pos_arus_kas.edit');
    Route::patch('/save', 'API/Akunting/Setup/PosisiLaporan/PosArusKasAPI@save')->name('api.akunting.setup.posisi_laporan.pos_arus_kas.save');
});