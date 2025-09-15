<?php

Route::group('pengaturan-dasar/level-approval', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'Konfigurasi/PengaturanDasar/LevelApproval@index')->name('pengaturan_dasar.level_approval');
});