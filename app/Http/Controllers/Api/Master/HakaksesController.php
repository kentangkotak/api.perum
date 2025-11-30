<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Menus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HakaksesController extends Controller
{
    public function index()
    {

        $hakakses = Menus::with(
            [
                'hakakses' => function ($q) {
                    $q->where('idwarga', request('idwarga'));
                }
            ]
        )
        ->orderBy('urut')
        ->get();

        return response()->json([
            'status' => true,
            'data' => $hakakses
        ]);
    }

    public function store(Request $r)
    {
        $idwarga = $r->id_warga;
        $menus   = $r->id_menu ?? [];

        // hapus hak akses lama
        try {
            DB::beginTransaction();
            $cek = DB::table('hakakses')->where('idwarga', $idwarga)->count();
            if($cek > 0){
                DB::table('hakakses')->where('idwarga', $idwarga)->delete();
            }

                // insert baru
                foreach ($menus as $idmenu) {
                    DB::table('hakakses')->insert([
                        'idwarga' => $idwarga,
                        'idmenu'  => $idmenu,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            DB::commit();
             $hakakses = Menus::with([
                'hakakses' => function ($q) use ($idwarga) {
                    $q->where('idwarga', $idwarga);
                }
            ])
             ->orderBy('urut')   // ← di
            ->get();
            return response()->json(['status' => true, 'data' => $hakakses, 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

    }


}
