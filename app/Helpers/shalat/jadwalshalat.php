<?php

namespace App\Helpers\shalat;

class jadwalshalat
{
    public static function jadwalToday(string $cityId,string $timezone)
    {
        $url = "https://api.myquran.com/v3/sholat/jadwal/" . $cityId . "/today?tz=" . urlencode($timezone);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'message' => $error,
                'data'    => null,
            ];
        }

        if ($status !== 200) {
            return [
                'success' => false,
                'message' => "HTTP Error {$status}",
                'data'    => null,
            ];
        }

        return [
            'success' => true,
            'data'    => json_decode($response, true),
        ];
    }
}
