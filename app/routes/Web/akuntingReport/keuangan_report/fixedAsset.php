<?php

Route::group('keuangan-report/fixed-asset', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'AkuntansiReport/KeuanganReport/FixedAsset@list')->name('keuangan_report.fixed_asset');
    Route::get('/cetak', 'AkuntansiReport/KeuanganReport/FixedAsset@cetak')->name('keuangan_report.fixed_asset.cetak');
});