<?php

Route::group('api/akunting/fixed-asset/lokasi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/FixedAssetAPI@list_lokasi')->name('api.akunting.fixed_asset.lokasi');
    Route::get('/create', 'API/Akunting/FixedAssetAPI@form_lokasi')->name('api.akunting.fixed_asset.lokasi.create');
    Route::get('/edit', 'API/Akunting/FixedAssetAPI@form_lokasi')->name('api.akunting.fixed_asset.lokasi.edit');
    Route::patch('/save', 'API/Akunting/FixedAssetAPI@save_lokasi')->name('api.akunting.fixed_asset.lokasi.save');
});

Route::group('api/akunting/fixed-asset/kategori', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/FixedAssetAPI@list_kategori')->name('api.akunting.fixed_asset.kategori');
    Route::get('/create', 'API/Akunting/FixedAssetAPI@form_kategori')->name('api.akunting.fixed_asset.kategori.create');
    Route::get('/edit', 'API/Akunting/FixedAssetAPI@form_kategori')->name('api.akunting.fixed_asset.kategori.edit');
    Route::patch('/save', 'API/Akunting/FixedAssetAPI@save_kategori')->name('api.akunting.fixed_asset.kategori.save');
});

Route::group('api/akunting/fixed-asset/', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::post('/{mytype}/cek-kode/{kode}', 'API/Akunting/FixedAssetAPI@cek_kode')->name('api.akunting.fixed_asset.cek_kode');

    Route::get('/list-data', 'API/Akunting/FixedAssetAPI@list')->name('api.akunting.fixed_asset');
    Route::get('/create', 'API/Akunting/FixedAssetAPI@form')->name('api.akunting.fixed_asset.create');
    Route::get('/edit', 'API/Akunting/FixedAssetAPI@form')->name('api.akunting.fixed_asset.edit');
    Route::patch('/save', 'API/Akunting/FixedAssetAPI@save')->name('api.akunting.fixed_asset.save');
    Route::patch('/approve', 'API/Akunting/FixedAssetAPI@approve')->name('api.akunting.fixed_asset.approve');
    Route::get('/ubah-lokasi', 'API/Akunting/FixedAssetAPI@ubah_lokasi')->name('api.akunting.fixed_asset.ubah_lokasi');
    Route::get('/ubah-lokasi/histori', 'API/Akunting/FixedAssetAPI@ubah_lokasi_histori')->name('api.akunting.fixed_asset.ubah_lokasi.histori');
    Route::patch('/save-ubah-lokasi', 'API/Akunting/FixedAssetAPI@save_ubah_lokasi')->name('api.akunting.fixed_asset.save_ubah_lokasi');
    Route::get('/revaluate', 'API/Akunting/FixedAssetAPI@revaluate')->name('api.akunting.fixed_asset.revaluate');
    Route::patch('/save-revaluate', 'API/Akunting/FixedAssetAPI@save_revaluate')->name('api.akunting.fixed_asset.save_revaluate');
    Route::get('/write-off', 'API/Akunting/FixedAssetAPI@write_off')->name('api.akunting.fixed_asset.write_off');
    Route::patch('/save-write-off', 'API/Akunting/FixedAssetAPI@save_write_off')->name('api.akunting.fixed_asset.save_write_off');
});

Route::group('api/akunting/fixed-asset/depresiasi', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::patch('proses', 'API/Akunting/FixedAssetAPI@proses_depresiasi')->name('api.akunting.fixed_asset.depresiasi.proses');
});