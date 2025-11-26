<?php

Route::group('inventori/konfirmasi-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Inventori/KonfirmasiDistribusi@list')->name('inventori.konfirmasi_distribusi');
});