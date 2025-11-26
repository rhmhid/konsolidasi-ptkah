<?php

Route::group('api/hutang-report/aging-hutang-unbill', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/HutangReport/AgingHutangUnbillAPI@excel')->name('api.hutang_report.aging_hutang_unbill.excel');
});