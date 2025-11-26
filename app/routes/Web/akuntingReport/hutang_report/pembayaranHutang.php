<?php

Route::group('hutang-report/pembayaran-hutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/HutangReport/PembayaranHutang@list')->name('hutang_report.pembayaran_hutang');
    Route::get('/cetak', 'AkuntansiReport/HutangReport/PembayaranHutang@cetak')->name('hutang_report.pembayaran_hutang.cetak');
});