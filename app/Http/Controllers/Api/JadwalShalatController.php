<?php

namespace App\Http\Controllers\Api;

use App\Helpers\shalat\jadwalshalat;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class JadwalShalatController extends Controller
{
    public function today()
    {


        $kota = '7a614fd06c325499f1680b9896beedeb';
        $timezone = 'Asia/Jakarta';

        $result = jadwalshalat::jadwalToday($kota, $timezone);
        return new JsonResponse($result);
    }
}
