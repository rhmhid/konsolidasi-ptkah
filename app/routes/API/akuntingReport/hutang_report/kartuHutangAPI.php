<?php

Route::group('api/hutang-report/kartu-hutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/HutangReport/KartuHutangAPI@excel')->name('api.hutang_report.kartu_hutang.excel');
});