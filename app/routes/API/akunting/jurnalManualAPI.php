<?php

Route::group('api/akunting/jurnal-manual', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Akunting/JurnalManualAPI@list')->name('api.akunting.jurnal_manual');
    Route::get('/create', 'API/Akunting/JurnalManualAPI@create')->name('api.akunting.jurnal_manual.create');
    Route::get('/template', 'API/Akunting/JurnalManualAPI@template')->name('api.akunting.jurnal_manual.tpl');
    Route::post('/{myid}/posting', 'API/Akunting/JurnalManualAPI@posting')->name('api.akunting.jurnal_manual.posting');
    Route::patch('/save/{mytype}', 'API/Akunting/JurnalManualAPI@save')->name('api.akunting.jurnal_manual.save');
    Route::post('/{myid}/delete', 'API/Akunting/JurnalManualAPI@delete')->name('api.akunting.jurnal_manual.delete');
    Route::post('parsing-excel', 'API/Akunting/JurnalManualAPI@parsing_excel')->name('api.akunting.jurnal_manual.parsing_excel');
});