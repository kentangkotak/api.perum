<?php

namespace App\Http\Controllers\Api\Notif;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class simpannotif extends Controller
{
    public static function simpannotifx($id_penerima,$user,$validate,$notrans){

       foreach ($id_penerima as $uid) {
            $notif = Notifikasi::create([
                'user_id' => $user,
                'user_penerima' => $uid,
                'title' => 'Pembayaran Iuran Berhasil',
                'message' => "Diterima Iuran dari {$validate['nama']} untuk bulan {$validate['bulan']} tahun {$validate['tahun']}.",
                'type' => 'pembayaran_iuran',
                'data_json' => json_encode([ 'notrans' => $notrans ]), // sementara
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // update data_json dengan id unik dari DB
            $notif->data_json = json_encode([
                'notrans' => $notrans,
                'id' => $notif->id
            ]);
            $notif->save();
            return $notif->toArray();
        }

    }

}
