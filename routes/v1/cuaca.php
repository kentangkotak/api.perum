<?php

use App\Http\Controllers\Api\CuacaController as ApiCuacaController;
use App\Http\Controllers\Api\JadwalShalatController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    // 'prefix' => ''
], function () {
   Route::get('/cuaca', [ApiCuacaController::class, 'getCuaca']);
   Route::get('/jadwalshalat', [JadwalShalatController::class, 'today']);
});
