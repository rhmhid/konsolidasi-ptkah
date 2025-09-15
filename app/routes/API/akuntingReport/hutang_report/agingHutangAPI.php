<?php

Route::group('api/hutang-report/aging-hutang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/HutangReport/AgingHutangAPI@excel')->name('api.hutang_report.aging_hutang.excel');
});