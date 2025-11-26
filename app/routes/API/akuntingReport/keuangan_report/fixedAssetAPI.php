<?php

Route::group('api/keuangan-report/fixed-asset', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/AkuntansiReport/KeuanganReport/FixedAssetAPI@excel')->name('api.keuangan_report.fixed_asset.excel');
});