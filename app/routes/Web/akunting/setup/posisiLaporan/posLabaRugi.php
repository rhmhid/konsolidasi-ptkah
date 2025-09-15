<?php

Route::group('akunting/setup/posisi-laporan/pos-laba-rugi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/PosisiLaporan/Pospl@index')->name('akunting.setup.posisi_laporan.pos_pl');
});