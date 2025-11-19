<?php

use App\Http\Controllers\Api\CuacaController as ApiCuacaController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:api', // JWT au
    // 'prefix' => ''
], function () {
   Route::get('/cuaca', [ApiCuacaController::class, 'getCuaca']);
});
