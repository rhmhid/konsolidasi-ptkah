<?php

Route::group('api/pengaturan-dasar/control-panel', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/list-data', 'API/Konfigurasi/PengaturanDasar/ControlPanelAPI@list_data_configs')->name('api.pengaturan_dasar.control_panel');
    Route::get('/edit', 'API/Konfigurasi/PengaturanDasar/ControlPanelAPI@edit_configs')->name('api.pengaturan_dasar.control_panel.edit');
    Route::patch('/save', 'API/Konfigurasi/PengaturanDasar/ControlPanelAPI@save_configs')->name('api.pengaturan_dasar.control_panel.save');
});