<?php

Route::group('api/inventori/konfirmasi-barang', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Inventori/KonfirmasiDistribusiAPI@list')->name('api.inventori.konfirmasi_distribusi');
    Route::get('/{myid}/create', 'API/Inventori/KonfirmasiDistribusiAPI@create')->name('api.inventori.konfirmasi_distribusi.create');
    Route::patch('/save', 'API/Inventori/KonfirmasiDistribusiAPI@save')->name('api.inventori.konfirmasi_distribusi.save');
    Route::post('/{myid}/delete', 'API/Inventori/KonfirmasiDistribusiAPI@delete')->name('api.inventori.konfirmasi_distribusi.delete');
});