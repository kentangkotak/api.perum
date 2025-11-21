<?php

namespace App\Http\Controllers\Api\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Transaksi\BelanjaH;
use App\Models\Transaksi\Pembayaraniuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanYangSudahBayarController extends Controller
{
    public function index()
    {
        $bulan = request('bulan');
        $tahun = request('tahun');
        $data = Pembayaraniuran::select('iuran.*','users.name as nama')
            ->join('users', 'users.id', '=', 'iuran.warga_id')
            ->where('iuran.bulan', $bulan)
            ->where('iuran.tahun', $tahun)
            ->get();
        return new JsonResponse([
            'data' => $data,
        ]);
    }

    public function indexkas()
    {
        $bulan = request('bulan');
        $tahun = request('tahun');
        $bulanawal = $bulan - 1;
        if ($bulanawal == 0) {
            $bulanawal = 12;
            $tahunawal = $tahun - 1;
        } else {
            $tahunawal = $tahun;
        }
        $saldoawal = Saldo::whereMonth('tgltutup', $bulanawal)
            ->whereYear('tgltutup', $tahunawal)
            ->first();
        $masuk = Pembayaraniuran::select('iuran.*','users.name as nama')
            ->join('users', 'users.id', '=', 'iuran.warga_id')
            ->whereMonth('iuran.created_at', $bulan)
            ->whereYear('iuran.created_at', $tahun)
            ->get();
        $keluar = BelanjaH::with(
            [
                'rincian' => function ($q) {
                    $q->orderBy('id', 'desc');
                }
            ]
        )
            ->where('jenispembayaran', '!=', 'Hutang')
            ->whereMonth('tgl', $bulan)
            ->whereYear('tgl', $tahun)
            ->get();
        return new JsonResponse([
            'saldoawal' => $saldoawal,
            'masuk' => $masuk,
            'keluar' => $keluar,
        ]);
    }
}
