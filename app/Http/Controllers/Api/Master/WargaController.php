<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Hakakses;
use App\Models\Menus;
use App\Models\User;
use App\Models\Userrinci;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Net\SFTP;
use Symfony\Component\HttpFoundation\JsonResponse;

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
                // 'password' => bcrypt($validate['nokk']),
                // 'pass' => $validate['nokk'],
            ]);

            DB::commit();
            return new JsonResource([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return new JsonResponse([
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
        if($request->jenis === "Kartu Keluarga"){

            $validate = $request->validate([
                'id_heder' => 'required',
                'nokk' => 'required',
                'jenis' => 'required',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            ],[
                'id_heder.required' => 'Warga Harus Di isi.',
                'nokk.required' => 'No. KK Harus Di isi.',
                'jenis.required' => 'Jenis Dokumen Harus Di isi.',
                'foto.mimes' => 'Foto hanya boleh berupa file JPG, JPEG, atau PNG.',
                'foto.max' => 'Ukuran foto maksimal 1 MB.',
            ]);

            try {
                $cek = Userrinci::where('id_heder', $validate['id_heder'])->where('noktp', $validate['nokk'])->count();
                if($cek > 0){
                    return new JsonResource([
                        'status' => false,
                        'message' => 'Data sudah ada.',
                    ], 200);
                }else{
                    DB::beginTransaction();
                    $data =
                        [
                            'id_heder' => $validate['id_heder'],
                            'noktp' => $validate['nokk'],
                            'nama' => '',
                            'jenisdok' => $validate['jenis'],
                            'foto' => $validate['foto'],
                        ];
                        if ($request->hasFile('foto')) {
                            $file = $request->file('foto');
                            $ext = $file->getClientOriginalExtension();
                            $filename = $validate['nokk'] . '.' . $ext;

                            $folder = 'perumbi/' . $validate['id_heder'];
                            // if (!Storage::disk('sftp_storage')->exists($folder)) {
                            $sftp = new SFTP('192.168.33.105');
                                if (!$sftp->login('root', 'sasa0102')) {
                                    throw new \Exception('Login failed');
                                }

                                $folder = '/www/wwwroot/storage/perumbi/' . $validate['id_heder'];
                                if (!$sftp->is_dir($folder)) {
                                    $sftp->mkdir($folder, 0755, true);
                                }

                                $sftp->put("$folder/$filename", file_get_contents($file));
                            // }
                            // Storage::disk('sftp_storage')->put("$folder/{$filename}", file_get_contents($file));

                            $data['foto'] = 'https://perumbi.udumbara.my.id/perumbi/'.$validate['id_heder'].'/'.$filename;
                            $data['path'] = 'perumbi/'.$validate['id_heder'].'/'.$filename;
                        }

                        $simpan = Userrinci::create($data);
                    DB::commit();
                    $result = Userrinci::find($simpan->id);
                    return new JsonResource([
                        'status' => true,
                        'message' => 'Data berhasil disimpan',
                        'data' => $result,
                    ], 200);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }else{
            $validate = $request->validate([
                'id_heder' => 'required',
                'nama' => 'required',
                'noktp' => 'required',
                'jenis' => 'required',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:512',
            ],[
                'id_heder.required' => 'Warga Harus Di isi.',
                'nama.required' => 'Nama Harus Di isi.',
                'noktp.required' => 'No. KTP Harus Di isi.',
                'jenis.required' => 'Jenis Dokumen Harus Di isi.',
                'foto.mimes' => 'Foto hanya boleh berupa file JPG, JPEG, atau PNG.',
                'foto.max' => 'Ukuran foto maksimal 512 KB.',
            ]);

            try {
                $cek = Userrinci::where('id_heder', $validate['id_heder'])->where('noktp', $validate['noktp'])->count();
                if($cek > 0){
                    return new JsonResource([
                        'status' => false,
                        'message' => 'Data sudah ada.',
                    ], 200);
                }else{
                DB::beginTransaction();
                    $data =
                        [
                            'id_heder' => $validate['id_heder'],
                            'nama' => $validate['nama'],
                            'noktp' => $validate['noktp'],
                            'jenisdok' => $validate['jenis'],
                            'foto' => $validate['foto'],
                        ];
                        if ($request->hasFile('foto')) {
                            $file = $request->file('foto');
                            $ext = $file->getClientOriginalExtension();
                            $filename = $validate['noktp'] . '.' . $ext;

                            $folder = 'perumbi/' . $validate['id_heder'];
                            // if (!Storage::disk('sftp_storage')->exists($folder)) {
                            $sftp = new SFTP('192.168.33.105');
                                if (!$sftp->login('root', 'sasa0102')) {
                                    throw new \Exception('Login failed');
                                }

                                $folder = '/www/wwwroot/storage/perumbi/' . $validate['id_heder'];
                                if (!$sftp->is_dir($folder)) {
                                    $sftp->mkdir($folder, 0755, true);
                                }

                                $sftp->put("$folder/$filename", file_get_contents($file));
                            // }
                            // Storage::disk('sftp_storage')->put("$folder/{$filename}", file_get_contents($file));

                            $data['foto'] = 'https://perumbi.udumbara.my.id/perumbi/'.$validate['id_heder'].'/'.$filename;
                            $data['path'] = 'perumbi/'.$validate['id_heder'].'/'.$filename;
                        }

                        $simpan = Userrinci::create($data);
                    DB::commit();
                    $result = Userrinci::find($simpan->id);
                    return new JsonResource([
                        'status' => true,
                        'message' => 'Data berhasil disimpan',
                        'data' => $result,
                    ], 200);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }
    }

    public function hapusrinci(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required',
            'id_heder' => 'required',
            'foto' => 'required',
            'path' => 'required',
        ],[
            'id.required' => 'ID Harus Di isi.',
            'id_heder.required' => 'ID Heder Todak Boleh Kosong.',
            'foto.required' => 'Tidak Ada Foto Untuk Dihapus.',
            'path.required' => 'Tidak Ada Path Untuk Dihapus.',
        ]);
        try {
            DB::beginTransaction();

            // Ambil data userrinci
            $data = Userrinci::find($validate['id']);

            // Hapus file foto di storage
            // Contoh: storage/app/public/perumbi/xxx.jpg
            if (Storage::disk('sftp_storage')->exists($validate['path'])) {
                Storage::disk('sftp_storage')->delete($validate['path']);
            }

            // Hapus record di database
            $data->delete();

            DB::commit();
            $respon = self::getlistbyid($validate['id_heder']);
            return new JsonResponse([
                'status' => true,
                'message' => 'Data berhasil dihapus',
                'data' => $respon,
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return new JsonResponse([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public static function getlistbyid($id)
    {
        $data = User::whereNull('flaging')
                ->with('rincian')
                ->where('id', $id)
                ->first();
        return $data;
    }

    public function caridatawarga(Request $request)
    {
        $data = User::select('id', 'name', 'nokk')
                ->whereNull('flaging')
                ->where('nokk', $request->nokk)
                ->first();
        if ($data) {
            return new JsonResource([
                'status' => true,
                'message' => 'Data ditemukan',
                'data' => $data,
            ]);
        }else{
            return new JsonResource([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }

    }

    public function register(Request $request)
    {
        $validate = $request->validate([
            'nokk' => 'required',
            'username' => 'required',
            'password' => 'required',
            'confirmpassword' => 'required|same:password',
        ],[
            'nokk.required' => 'No. KK Harus Di isi.',
            'username.required' => 'Username Harus Di isi.',
            'password.required' => 'Password Harus Di isi.',
            'confirmpassword.required' => 'Konfirmasi Password Harus Di isi.',
            'confirmpassword.same' => 'Konfirmasi Password tidak sama.',
        ]);

        try {
            DB::beginTransaction();

            // cari user berdasarkan nokk
            $cari = User::where('username', $validate['username'])->first();

            if ($cari) {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Username sudah digunakan',
                ], 404);
            }

            // update user
            $user = User::where('nokk', $validate['nokk'])->first();
            $user->update([
                'username' => $validate['username'],
                'password' => bcrypt($validate['password']),
                'pass' => $validate['password'],
            ]);

            Hakakses::where('idwarga', $user->id)->delete();
            $menu = Menus::where('warga', '1')->get();
            foreach ($menu as $key => $value) {
                Hakakses::create([
                    'idwarga' => $user->id,
                    'idmenu' => $value->id,
                ]);
            }

            DB::commit();

            return response()->json([
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
