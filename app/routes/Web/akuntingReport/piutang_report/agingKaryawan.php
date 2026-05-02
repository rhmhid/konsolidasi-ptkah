<?php

Route::group('piutang-report/aging-karyawan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/PiutangReport/AgingKaryawan@list')->name('piutang_report.aging_karyawan');
    Route::get('/cetak', 'AkuntansiReport/PiutangReport/AgingKaryawan@cetak')->name('piutang_report.aging_karyawan.cetak');
    Route::get('detail', 'AkuntansiReport/PiutangReport/AgingKaryawan@detail')->name('piutang_report.aging_karyawan.detail');
});