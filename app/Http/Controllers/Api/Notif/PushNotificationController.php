<?php

namespace App\Http\Controllers\Api\Notif;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    protected $notifService;

    // Dependency Injection
    public function __construct(NotificationService $notifService)
    {
        $this->notifService = $notifService;
    }

    public function broadcast(Request $request)
    {
        // Ambil banyak token dari DB Laravel 10
        $tokens = FcmToken::distinct()->pluck('token')->toArray();

        // Kirim ke Laravel 12
        $result = $this->notifService->sendToLaravelNotif(
            $tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result);
    }
}
