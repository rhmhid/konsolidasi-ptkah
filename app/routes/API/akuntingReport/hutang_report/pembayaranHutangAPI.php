<?php

Route::group('api/hutang-report/pembayaran-hutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/HutangReport/PembayaranHutangAPI@excel')->name('api.hutang_report.pembayaran_hutang.excel');
});