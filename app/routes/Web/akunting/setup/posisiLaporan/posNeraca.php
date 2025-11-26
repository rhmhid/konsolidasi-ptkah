<?php

Route::group('akunting/setup/posisi-laporan/pos-neraca', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/Setup/PosisiLaporan/PosNeraca@index')->name('akunting.setup.posisi_laporan.pos_neraca');
});