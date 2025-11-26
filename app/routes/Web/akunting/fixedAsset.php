<?php

Route::group('akunting/fixed-asset/lokasi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/FixedAsset@lokasi')->name('akunting.fixed_asset.lokasi');
});

Route::group('akunting/fixed-asset/kategori', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/FixedAsset@kategori')->name('akunting.fixed_asset.kategori');
});

Route::group('akunting/fixed-asset/list', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/FixedAsset@list')->name('akunting.fixed_asset.list');
});

Route::group('akunting/fixed-asset/depresiasi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/FixedAsset@depresiasi')->name('akunting.fixed_asset.depresiasi');
});