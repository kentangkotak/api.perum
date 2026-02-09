<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected string $url;
    protected string $key;

    public function __construct()
    {
        // Mengambil config dari services.php
        $this->url = config('services.notif_server.url');
        $this->key = config('services.notif_server.key');
    }

    public function sendToLaravelNotif(array $tokens, string $title, string $body, array $data = [])
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-Internal-Key' => $this->key, // Pengaman antar server
            ])->post("{$this->url}/api/send-notification", [
                'tokens' => $tokens,
                'title'  => $title,
                'body'   => $body,
                'data'   => $data,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Gagal menghubungi Laravel 12: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Server Notif sedang bermasalah.'
            ];
        }
    }
}
