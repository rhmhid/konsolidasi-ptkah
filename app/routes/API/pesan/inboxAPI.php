<?php

Route::group('api/my-mail/my-inbox', ['middleware' => 'AuthMiddleware'], function ()
{
    Route::get('/', 'API/Pesan/InboxAPI@index')->name('api.mymail.myinbox');
    Route::get('/list', 'API/Pesan/InboxAPI@list')->name('api.mymail.myinbox.list');
});