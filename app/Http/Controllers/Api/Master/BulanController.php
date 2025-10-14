<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Bulan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BulanController extends Controller
{
    public function index()
    {
        $data = Bulan::all();
        return new JsonResource($data);
    }
}
