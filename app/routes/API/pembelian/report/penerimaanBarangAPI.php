<?php

Route::group('api/pembelian-report/penerimaan-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/excel', 'API/Pembelian/Report/PenerimaanBarangAPI@excel')->name('api.pembelian_report.penerimaan_barang.excel');
});