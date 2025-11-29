<?php

use App\Http\Controllers\Api\Master\WargaController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    'prefix' => 'master/warga'
], function () {
    Route::get('/get-warga', [WargaController::class, 'getlist']);
    Route::post('/simpan', [WargaController::class, 'store']);
    Route::post('/hapus', [WargaController::class, 'hapus']);
    Route::post('/simpanrinci', [WargaController::class, 'storerinci']);
    Route::post('/hapusrinci', [WargaController::class, 'hapusrinci']);
});
