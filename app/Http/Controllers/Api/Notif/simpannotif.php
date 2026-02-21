<?php

namespace App\Http\Controllers\Api\Notif;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Services\NotificationService;

class simpannotif extends Controller
{
    protected $notifService;

    // Masukkan service ke constructor
    public function __construct(NotificationService $notifService)
    {
        $this->notifService = $notifService;
    }

    public static function simpannotifx($title,$message,$type,$id_penerima,$user,$notrans){
        $dataInsert = [];
        foreach ($id_penerima as $uid) {
                $dataInsert[] = [
                'user_id' => $user,
                'user_penerima' => $uid,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data_json' => json_encode([
                    'notrans' => $notrans
                ]),
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        Notifikasi::insert($dataInsert);
        return true;
    }

    public static function kirimnotifx(
        $tokens,
        $title,
        $message,
        $type,
        $notrans
    ) {
        $service = app(NotificationService::class);

        return $service->sendToLaravelNotif(
            $tokens,
            $title,
            $message,
            [
                'notrans' => $notrans,
                'type' => $type
            ]
        );
    }

}
