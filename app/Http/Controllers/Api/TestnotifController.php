<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestnotifController extends Controller
{
    public function index()
    {
        $messages = ['Halo', 'Pesan realtime dari Laravel!', 'Notif ketiga'];
        broadcast(new \App\Events\TestEvent($messages));
        return response()->json([
            'message' => $messages,
        ]);
    }
}
