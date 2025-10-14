<?php

namespace App\Http\Controllers\Api\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Transaksi\Pembayaraniuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranIuranController extends Controller
{
    public function index()
    {
        $data = Pembayaraniuran::select('iuran.*','users.name as nama')
            ->join('users', 'users.id', '=', 'iuran.warga_id')
            ->whereMonth('iuran.created_at', request('bulan'))
            ->whereYear('iuran.created_at', request('tahun'))
            ->when(request('q'), function ($q) {
                $q->where('iuran.notrans', 'like', '%' . request('q') . '%')
                    ->orWhere('users.name', 'like', '%' . request('q') . '%');
            })
            ->get();
        return new JsonResponse($data);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'warga_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'jeniskewargaan' => 'required',
            'jumlah' => 'required',
            'carabayar' => 'required',
            'keterangan' => 'nullable',
        ],[
            'warga_id.required' => 'Warga Harus Di isi.',
            'bulan.required' => 'Bulan Harus Di isi.',
            'tahun.required' => 'Tahun Harus Di isi.',
            'jeniskewargaan.required' => 'Jenis Kewargaan Harus Di isi.',
            'jumlah.required' => 'Nominal Harus Di isi.',
            'carabayar.required' => 'Cara Bayar Harus Di isi.',
        ]);

        $cek = Pembayaraniuran::where('warga_id', $validate['warga_id'])
            ->where('bulan', $validate['bulan'])
            ->where('tahun', $validate['tahun'])
            ->first();

        if ($cek) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Data sudah ada',
            ], 400);
        }
        if($request->notrans === null || $request->notrans === ''){
            $notrans = 'TRX-' . date('YmdHis');
        }else{
            $notrans = $request->notrans;
        }
        $user = Auth::user();
        try {
            DB::beginTransaction();
                $simpan = Pembayaraniuran::updateOrCreate(
                    [
                        'notrans' => $notrans,
                    ],
                    [
                        'warga_id' => $validate['warga_id'],
                        'bulan' => $validate['bulan'],
                        'tahun' => $validate['tahun'],
                        'jeniskewargaan' => $validate['jeniskewargaan'],
                        'nominal' => $validate['jumlah'],
                        'cara_bayar' => $validate['carabayar'],
                        'keterangan' => $validate['keterangan'],
                        'user' => $user->id,
                    ]
                );
            DB::commit();
            $data = self::getlistbynotrans($notrans);
            return new JsonResponse([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return new JsonResponse([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public static function getlistbynotrans($notrans)
    {
        $data = Pembayaraniuran::select('iuran.*','users.name as nama')
            ->join('users', 'users.id', '=', 'iuran.warga_id')
            ->where('iuran.notrans', $notrans)
            ->get();
        return $data;
    }
}
