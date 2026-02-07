<?php

namespace App\Http\Controllers\Api\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Transaksi\BelanjaH;
use App\Models\Transaksi\BelanjaR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class BelanjaController extends Controller
{
    public function index()
    {
        $data = BelanjaH::with(
            [
                'rincian' => function ($q) {
                    $q->orderBy('id', 'desc');
                }
            ]
        )
            ->whereMonth('belanjaH.tgl', request('bulan'))
            ->whereYear('belanjaH.tgl', request('tahun'))
            ->when(request('q'), function ($q) {
                $q->where('belanjaH.notrans', 'like', '%' . request('q') . '%')
                    ->orWhere('belanjaH.jenisbelanja', 'like', '%' . request('q') . '%')
                    ->orWhere('belanjaH.keterangan', 'like', '%' . request('q') . '%');
            })
            ->orderBy('belanjaH.tgl', 'desc')
            ->get();
        return new JsonResponse([
            'data' => $data]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'tgl' => 'required',
            'jenisbelanja' => 'required',
            'keterangan' => 'required',
            'jenispembayaran' => 'required',
            'totalbelanja' => 'required',
            'namabarang' => 'required',
            'jumlah' => 'required',
            'harga' => 'required',
            'satuan' => 'required',
            'subtotal' => 'required',
        ],[
            'tgl.required' => 'Tanggal Harus Di isi.',
            'jenisbelanja.required' => 'Jenis Belanja Harus Di isi.',
            'jenispembayaran.required' => 'Jenis Pembayaran Harus Di isi.',
            'totalbelanja.required' => 'Total Belanja Harus Di isi.',
            'namabarang.required' => 'Nama Barang Harus Di isi.',
            'jumlah.required' => 'Jumlah Harus Di isi.',
            'satuan.required' => 'Satuan Harus Di isi.',
            'harga.required' => 'Harga Harus Di isi.',
            'subtotal.required' => 'Subtotal Harus Di isi.',
        ]);

        if($request->notrans === null || $request->notrans === ''){
            $notrans = 'B-' . date('YmdHis');
        }else{
            $notrans = $request->notrans;
        }
        $user = Auth::user();
        try {
            DB::beginTransaction();
                $simpan = BelanjaH::updateOrCreate(
                    [
                        'notrans' => $notrans,
                    ],
                    [
                        'tgl' => $validate['tgl'],
                        'jenisbelanja' => $validate['jenisbelanja'],
                        'keterangan' => $validate['keterangan'],
                        'jenispembayaran' => $validate['jenispembayaran'],
                        'totalbelanja' => $validate['totalbelanja'] + $validate['subtotal'],
                        'user' => $user->id,
                    ]
                );
                $rincian = BelanjaR::create(
                    [
                        'notrans' => $notrans,
                        'namabarang' => $validate['namabarang'],
                        'jumlah' => $validate['jumlah'],
                        'satuan' => $validate['satuan'],
                        'harga' => $validate['harga'],
                        'subtotal' => $validate['subtotal'],
                        'user' => $user->id,
                    ]
                );
            DB::commit();
            $data = self::getbelanjabynotrans($notrans);
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

    public static function getbelanjabynotrans($notrans)
    {
        $data = BelanjaH::with(
            [
                'rincian'  => function ($q) {
                    $q->orderBy('id', 'desc')->limit(1);
                }
            ]
        )
            ->where('notrans', $notrans)
            ->get();
        return $data;
    }

    public function hapusrincianbelanja(Request $request)
    {
        $id = $request->id;
        $notrans = $request->notrans;
        $totalbelanja = $request->totalbelanja;

      try{
        DB::beginTransaction();
            $hapus = BelanjaR::where('id', $id)->delete();
            $update = BelanjaH::where('notrans', $notrans)->first();
            $update->totalbelanja = $totalbelanja;
            $update->save();
        DB::commit();
            $data = self::getbelanjabynotrans($notrans);
             return new JsonResponse([
                'status' => true,
                'message' => 'Data berhasil dihapus',
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

}
