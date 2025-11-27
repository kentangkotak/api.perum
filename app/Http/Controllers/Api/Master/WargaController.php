<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userrinci;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WargaController extends Controller
{
    public function getlist()
    {
        $data = User::whereNull('flaging')
                ->with('rincian')
                ->where(function ($q) {
                    $q->where('username', '!=', 'sa')
                    ->orWhereNull('username')
                    ->orWhere('username', '');
                })
                ->when(request('q'), function ($q) {
                    $q->where('name', 'like', '%' . request('q') . '%')
                    ->orWhere('nokk', 'like', '%' . request('q') . '%');
                })
                ->get();
        return new JsonResource($data);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama' => 'required|string',
            'nokk' => 'required|string',
            // 'username' => 'required|string',
            // 'email' => 'required|email',
            // 'password' => 'required|string',
        ],[
            'nama.required' => 'Nama Harus Di isi.',
            'nokk.required' => 'No. KK Harus Di isi.',
        ]);

        try {
            DB::beginTransaction();
            $email = date('YmdHis') . '@warga.com';

            $user = User::updateOrCreate([
                'id' => $request->id,
            ],[
                'name' => $validate['nama'],
                'email' => $email,
                'nokk' => $validate['nokk'],
                // 'username' => $validate['nik'],
                'password' => bcrypt($validate['nokk']),
                'pass' => $validate['nokk'],
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
    public function hapus(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();
            $user = User::find($validate['id']);
            $user->update([
                'flaging' => 1,
            ]);
            DB::commit();
            return new JsonResource([
                'status' => true,
                'message' => 'Data berhasil dihapus',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    public function storerinci(Request $request)
    {
        $validate = $request->validate([
            'id_heder' => 'required',
            'nama' => 'required',
            'noktp' => 'required',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:512',
        ],[
            'id_heder.required' => 'Warga Harus Di isi.',
            'nama.required' => 'Nama Harus Di isi.',
            'noktp.required' => 'No. KTP Harus Di isi.',
            'foto.mimes' => 'Foto hanya boleh berupa file JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 512 KB.',
        ]);
        try {
            DB::beginTransaction();
            $data =
                [
                    'id_heder' => $validate['id_heder'],
                    'nama' => $validate['nama'],
                    'noktp' => $validate['noktp'],
                    'foto' => $validate['foto'],
                ];
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $ext = $file->getClientOriginalExtension();
                    $filename = $validate['noktp'] . '.' . $ext;

                    $folder = 'perumbi/' . $validate['id_heder'];
                    // Storage::disk('sftp_storage')->putFileAs('', $file, $filename);
                    Storage::disk('sftp_storage')->put("$folder/{$filename}", file_get_contents($file));

                    $data['foto'] = 'https://perumbi.udumbara.my.id/'.$folder.'/'.$filename;
                }
                $simpan = Userrinci::create($data);
            DB::commit();
            return new JsonResource([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $simpan,
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
