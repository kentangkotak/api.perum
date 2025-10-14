<?php

use App\Http\Controllers\Api\Master\BulanController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'master/bulan'
], function () {
    Route::get('/get-bulan', [BulanController::class, 'index']);
});
