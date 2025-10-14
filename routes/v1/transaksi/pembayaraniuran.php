<?php

use App\Http\Controllers\Api\Transaksi\PembayaranIuranController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'transaksi/pembayaraniuran'
], function () {
    Route::get('/get-pembayaran-iuran', [PembayaranIuranController::class, 'index']);
    Route::post('/simpan', [PembayaranIuranController::class, 'store']);
});
