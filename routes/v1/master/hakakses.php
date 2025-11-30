<?php

use App\Http\Controllers\Api\Master\BulanController;
use App\Http\Controllers\Api\Master\HakaksesController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'master/hakakses'
], function () {
    Route::get('/get-hakakses', [HakaksesController::class, 'index']);
    Route::post('/simpan', [HakaksesController::class, 'store']);
});
