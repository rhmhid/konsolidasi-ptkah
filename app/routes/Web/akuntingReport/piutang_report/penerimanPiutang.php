<?php

Route::group('piutang-report/penerimaan-piutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/PiutangReport/PenerimaanPiutang@list')->name('piutang_report.penerimaan_piutang');
    Route::get('/cetak', 'AkuntansiReport/PiutangReport/PenerimaanPiutang@cetak')->name('piutang_report.penerimaan_piutang.cetak');
});