<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class WargaController extends Controller
{
    public function getlist()
    {
        $data = User::whereNull('flaging')
                ->where(function ($q) {
                    $q->where('username', '!=', 'sa')
                    ->orWhereNull('username')
                    ->orWhere('username', '');
                })
                ->when(request('q'), function ($q) {
                    $q->where('name', 'like', '%' . request('q') . '%')
                    ->orWhere('nik', 'like', '%' . request('q') . '%');
                })
                ->get();
        return new JsonResource($data);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama' => 'required|string',
            'nik' => 'required|string',
            // 'username' => 'required|string',
            // 'email' => 'required|email',
            // 'password' => 'required|string',
        ],[
            'nama.required' => 'Nama Harus Di isi.',
            'nik.required' => 'No. KTP Harus Di isi.',
        ]);

        try {
            DB::beginTransaction();
            $email = $validate['nik'] . '@warga.com';

            $user = User::updateOrCreate([
                'id' => $request->id,
            ],[
                'name' => $validate['nama'],
                'email' => $email,
                'nik' => $validate['nik'],
                // 'username' => $validate['nik'],
                'password' => bcrypt($validate['nik']),
                'pass' => $validate['nik'],
            ]);

            DB::commit();
            return new JsonResource([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }


    }
}
