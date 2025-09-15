<?php

Route::group('pembelian/penerimaan-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Pembelian/PenerimaanBarang@list')->name('pembelian.penerimaan_barang');
    Route::get('/{myid}/cetak', 'Pembelian/PenerimaanBarang@cetak')->name('pembelian.penerimaan_barang.cetak');
});