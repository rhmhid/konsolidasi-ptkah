<?php

Route::group('api/piutang-report/aging-karyawan', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/PiutangReport/AgingKaryawanAPI@excel')->name('api.piutang_report.aging_karyawan.excel');
    Route::get('/detail/excel', 'API/AkuntansiReport/PiutangReport/AgingKaryawanAPI@excel_detail')->name('api.piutang_report.aging_karyawan.detail.excel');
});