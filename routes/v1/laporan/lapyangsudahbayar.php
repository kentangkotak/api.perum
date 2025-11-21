<?php

use App\Http\Controllers\Api\Laporan\LaporanYangSudahBayarController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'laporan/lapyangsudahbayar'
], function () {
    Route::get('/get-lap-pembayaran-iuran', [LaporanYangSudahBayarController::class, 'index']);
    Route::get('/get-lap-kas', [LaporanYangSudahBayarController::class, 'indexkas']);
});
