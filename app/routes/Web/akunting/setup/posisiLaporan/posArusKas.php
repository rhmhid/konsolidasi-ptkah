<?php

Route::group('akunting/setup/posisi-laporan/pos-arus-kas', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/PosisiLaporan/PosArusKas@index')->name('akunting.setup.posisi_laporan.pos_arus_kas');
});