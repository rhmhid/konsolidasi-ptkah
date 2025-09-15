<?php

Route::group('akunting/petty-cash/transaction-type', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/PettyCash@type')->name('akunting.petty_cash.transaction_type');
});

Route::group('akunting/petty-cash/transaction', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/PettyCash@list')->name('akunting.petty_cash.transaction');
    Route::get('/{myid}/cetak', 'Akunting/PettyCash@cetak')->name('akunting.petty_cash.transaction.cetak');
});