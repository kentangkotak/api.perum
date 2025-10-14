<?php

use App\Http\Controllers\Api\Master\BulanController;
use App\Http\Controllers\Api\Master\NominaliuranController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'master/nominaliuran'
], function () {
    Route::get('/get-iuran', [NominaliuranController::class, 'index']);
});
