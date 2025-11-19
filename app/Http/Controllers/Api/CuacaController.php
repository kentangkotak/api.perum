<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\JsonResponse;

class CuacaController extends Controller
{
    public function getCuaca()
    {
        $url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=35.74.05.1006";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return response()->json(['curl_error' => $error], 500);
    }

    return json_decode($response, true);
    }

}
