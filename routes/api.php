<?php

use App\Events\TestEvent;
use App\Events\UserMessage;
use App\Helpers\Routes\RouteHelper;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestnotifController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/testnotif', [TestnotifController::class, 'index']);

Route::get('/test-event', function() {
    broadcast(new TestEvent('Pesan realtime dari Laravel!'));
    return 'Event dikirim!';
});

Route::post('/send-message', function (\Illuminate\Http\Request $request) {
    $message = $request->input('message', 'Halo dari Laravel 🚀');

    broadcast(new UserMessage($message));

    return response()->json([
        'status'  => 'ok',
        'message' => $message,
    ]);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    RouteHelper::includeRouteFiles(__DIR__ . '/v1');
});



