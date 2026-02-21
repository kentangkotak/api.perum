<?php

namespace App\Http\Controllers\Api\Notif;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Notif\simpannotif as NotifSimpannotif;
use Illuminate\Support\Facades\Auth;

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

    public function fcmtokens()
    {
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

    public function show($id)
    {
       $notif = Notifikasi::find($id);
        if (!$notif) {
            return response()->json(['status' => false, 'message' => 'Notif tidak ditemukan'], 404);
        }
        return response()->json($notif);
    }

    public function listtoken()
    {
        $tokens = FcmToken::with('user')->get();

        return new JsonResponse([
            'status' => true,
            'data' => $tokens
        ]);
    }

    public function hapusalltoken()
    {
        DB::statement('CALL hapus_all_token()');

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function notificationsall()
    {
        $data = Notifikasi::with([
            'user',
            'userPenerima'
        ])->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function kirimnotifikasiall()
    {

        $type = 'test_notifikasi';
        $title = 'Test Notifikasi';

        $message = "Test Notifikasi,jika anda menerima notif ini,berrti server notifikasi sedang melakukan proses maintenance,mohon maaf atas ketidaknyamanannya.";
        $user = Auth::user();
        $notrans = 'COBA-' . date('YmdHis');

        $tokens = FcmToken::distinct()->pluck('token')->toArray();
        $id_penerima = FcmToken::distinct()->pluck('user_id')->toArray();

        $respnotif =NotifSimpannotif::simpannotifx($title,$message,$type,$id_penerima,$user->id,$notrans);

        NotifSimpannotif::kirimnotifx($tokens,$title,$message,$type,$notrans);
        // return $respnotif;
        if($respnotif){
            return response()->json([
                'status' => true,
                'message' => 'Notifikasi berhasil dikirim'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Notifikasi gagal dikirim'
            ]);
        }
    }
    public function hapusnotificationsall()
    {
        DB::statement('CALL hapus_all_notifikasi()');

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

}
