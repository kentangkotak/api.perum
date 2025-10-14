<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Nominaliuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NominaliuranController extends Controller
{
    public function index()
    {
        $data = Nominaliuran::all();
        return new JsonResponse($data);
    }
}
