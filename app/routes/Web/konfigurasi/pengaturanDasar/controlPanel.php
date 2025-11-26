<?php

Route::group('pengaturan-dasar/control-panel', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Konfigurasi/PengaturanDasar/ControlPanel@index')->name('pengaturan_dasar.control_panel');
});