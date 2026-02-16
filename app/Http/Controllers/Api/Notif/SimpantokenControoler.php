<?php

namespace App\Http\Controllers\Api\Notif;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Notifikasi;
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

    public function index()
    {
        $data = Notifikasi::where('user_penerima', auth()->user()->id)->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function readnotifications(Request $request)
    {
        $id = $request->id;

        $data = Notifikasi::where('id', $id)->first();
        $data->is_read = 1;
        $data->save();

        $result = Notifikasi::where('id', $id)->get();

        return response()->json([
            'data' => $result
        ]);
    }

    public function countUnread()
    {
        $count = Notifikasi::where('user_penerima', auth()->id())
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'status' => true,
            'total_unread' => $count
        ]);
    }

    public function readall()
    {
        $data = Notifikasi::where('user_penerima', auth()->id())
        ->where('is_read', 0)
        ->update([
            'is_read' => 1
        ]);


        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

}
