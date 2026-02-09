<?php

use App\Http\Controllers\Api\Master\BulanController;
use App\Http\Controllers\Api\Notif\SimpantokenControoler;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'notif/simpantoken'
], function () {
    Route::post('/simpantoken', [SimpantokenControoler::class, 'saveToken']);
    Route::get('/fcm-tokens', [SimpantokenControoler::class, 'fcmtokens']);

});
