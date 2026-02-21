<?php

use App\Http\Controllers\Api\Notif\SimpantokenControoler;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'notif/simpantoken'
], function () {
    Route::post('/simpantoken', [SimpantokenControoler::class, 'saveToken']);
    Route::get('/fcm-tokens', [SimpantokenControoler::class, 'fcmtokens']);

    Route::get('/notifications', [SimpantokenControoler::class, 'index']);
    Route::post('/read-notifications', [SimpantokenControoler::class, 'readnotifications']);
    Route::get('/unread-count', [SimpantokenControoler::class, 'countUnread']);
    Route::get('/read-all', [SimpantokenControoler::class, 'readall']);

    Route::get('/notifications/{id}', [SimpantokenControoler::class, 'show']);

    Route::get('/list-token', [SimpantokenControoler::class, 'listtoken']);

    Route::get('/hapus-all-token', [SimpantokenControoler::class, 'hapusalltoken']);

    Route::get('/notifications-all', [SimpantokenControoler::class, 'notificationsall']);
    Route::get('/kirim-notifikasi-all', [SimpantokenControoler::class, 'kirimnotifikasiall']);
    Route::get('/hapus-all-notifikasi-all', [SimpantokenControoler::class, 'hapusnotificationsall']);





});
