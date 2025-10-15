<?php

namespace App\Http\Controllers\Api\Laporan;

use App\Http\Controllers\Controller;
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
}
