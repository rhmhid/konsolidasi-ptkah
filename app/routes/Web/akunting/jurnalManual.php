<?php

Route::group('akunting/jurnal-manual', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Akunting/JurnalManual@list')->name('akunting.jurnal_manual');
});