<?php

namespace App\Http\Controllers\Api\Notif;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimpantokenControoler extends Controller
{
    public function saveToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = auth()->user();

        $user->fcmTokens()->updateOrCreate(
            ['token' => $request->token],
            [
                'platform' => $request->platform ?? 'web',
                'device_name' => $request->device_name,
                'last_used_at' => now()
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'FCM token saved'
        ]);
    }

    public function fcmtokens(){
        $tokens = FcmToken::all();

        return new JsonResponse([
            'status' => true,
            'data' => $tokens
        ]);
    }

}
