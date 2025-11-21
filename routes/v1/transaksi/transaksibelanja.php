<?php

use App\Http\Controllers\Api\Transaksi\BelanjaController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'transaksi/transaksibelanja'
], function () {
    Route::get('/get-belanja', [BelanjaController::class, 'index']);
    Route::post('/simpan', [BelanjaController::class, 'store']);
});
