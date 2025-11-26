<?php

Route::group('api/akunting/petty-cash/transaction-type', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/PettyCashAPI@list_type')->name('api.akunting.petty_cash.transaction_type');
    Route::get('/create', 'API/Akunting/PettyCashAPI@form_type')->name('api.akunting.petty_cash.transaction_type.create');
    Route::get('/edit', 'API/Akunting/PettyCashAPI@form_type')->name('api.akunting.petty_cash.transaction_type.edit');
    Route::patch('/save', 'API/Akunting/PettyCashAPI@save_type')->name('api.akunting.petty_cash.transaction_type.save');
});

Route::group('api/akunting/petty-cash/transaction', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/PettyCashAPI@list')->name('api.akunting.petty_cash.transaction');
    Route::get('/create', 'API/Akunting/PettyCashAPI@form')->name('api.akunting.petty_cash.transaction.create');
    Route::get('/edit', 'API/Akunting/PettyCashAPI@form')->name('api.akunting.petty_cash.transaction.edit');
    Route::get('/check-trans', 'API/Akunting/PettyCashAPI@cari_trans')->name('api.akunting.petty_cash.transaction.cari_trans');
    Route::get('/{mybank}/check-saldo', 'API/Akunting/PettyCashAPI@check_saldo')->name('api.akunting.petty_cash.transaction.check_saldo');
    Route::patch('/save', 'API/Akunting/PettyCashAPI@save')->name('api.akunting.petty_cash.transaction.save');
    Route::post('/{myid}/delete', 'API/Akunting/PettyCashAPI@delete')->name('api.akunting.petty_cash.transaction.delete');
});