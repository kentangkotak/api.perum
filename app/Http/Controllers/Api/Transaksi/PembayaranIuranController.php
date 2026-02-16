<?php

namespace App\Http\Controllers\Api\Transaksi;

use App\Helpers\simpannotif\simpannotif;
use App\Http\Controllers\Api\Notif\simpannotif as NotifSimpannotif;
use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Notifikasi;
use App\Models\Transaksi\Pembayaraniuran;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranIuranController extends Controller
{
    protected $notifService;

    // Masukkan service ke constructor
    public function __construct(NotificationService $notifService)
    {
        $this->notifService = $notifService;
    }

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
            'nama' => 'nullable',
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
                // $simpan = Pembayaraniuran::updateOrCreate(
                //     [
                //         'notrans' => $notrans,
                //     ],
                //     [
                //         'warga_id' => $validate['warga_id'],
                //         'bulan' => $validate['bulan'],
                //         'tahun' => $validate['tahun'],
                //         'jeniskewargaan' => $validate['jeniskewargaan'],
                //         'nominal' => $validate['jumlah'],
                //         'cara_bayar' => $validate['carabayar'],
                //         'keterangan' => $validate['keterangan'],
                //         'users' => $user->id,
                //     ]
                // );
            DB::commit();
            $tokens = FcmToken::distinct()->pluck('token')->toArray();
            $id_penerima = FcmToken::distinct()->pluck('user_id')->toArray();
            $respnotif =NotifSimpannotif::simpannotifx($id_penerima,$user->id,$validate,$notrans);


            // Simpan ke tabel notifications
            // $dataInsert = [];
            // foreach ($id_penerima as $uid) {
            //      $dataInsert[] = [
            //         'user_id' => $user->id,
            //         'user_penerima' => $uid->user_id,
            //         'title' => 'Pembayaran Iuran Berhasil',
            //         'message' => "Diterima Iuran dari {$validate['nama']} untuk bulan {$validate['bulan']} tahun {$validate['tahun']}.",
            //         'type' => 'pembayaran_iuran',
            //         'data_json' => json_encode([
            //             'notrans' => $notrans
            //         ]),
            //         'is_read' => 0,
            //         'created_at' => now(),
            //         'updated_at' => now()
            //     ];
            // }
            // Notifikasi::insert($dataInsert);

            if (!empty($tokens)) {
                $res = $this->notifService->sendToLaravelNotif(
                    $tokens,
                    "Pembayaran Iuran Berhasil", // Title
                    "Diterima Iuran dari {$validate['nama']} untuk bulan {$validate['bulan']} tahun {$validate['tahun']}.", // Body
                    [
                        'notrans' => $notrans,
                        // 'id' => $respnotif->id,
                        'type' => 'pembayaran_iuran'
                    ] // Data tambahan
                );
                // Log::info('Respon dari Laravel 12: ', [$res]);
            }
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
