<?php

Route::group('api/inventori/manajemen-stok/stock-adjusment', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/ManajemenStok/StockAdjusmentAPI@list')->name('api.inventori.manajemen_stok.stock_adjusment');
    Route::get('/list-stock', 'API/Inventori/ManajemenStok/StockAdjusmentAPI@list_stock')->name('api.inventori.manajemen_stok.stock_adjusment.stock');
    Route::get('/create', 'API/Inventori/ManajemenStok/StockAdjusmentAPI@create')->name('api.inventori.manajemen_stok.stock_adjusment.create');
    Route::patch('/save', 'API/Inventori/ManajemenStok/StockAdjusmentAPI@save')->name('api.inventori.manajemen_stok.stock_adjusment.save');
    Route::post('/{myid}/delete', 'API/Inventori/ManajemenStok/StockAdjusmentAPI@delete')->name('api.inventori.manajemen_stok.stock_adjusment.delete');
});