<?php

Route::group('inventori/manajemen-stok/stock-adjusment', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/ManajemenStok/StockAdjusment@list')->name('inventori.manajemen_stok.stock_adjusment');
    Route::get('/stock', 'Inventori/ManajemenStok/StockAdjusment@stock')->name('inventori.manajemen_stok.stock_adjusment.stock');
});