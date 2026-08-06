<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini
    |--------------------------------------------------------------------------
    |
    | Dipakai Fitur 01 (membaca poster) dan Fitur 04 (skor kecocokan).
    | Kunci diambil dari .env dan HANYA dibaca di berkas config ini.
    |
    | Jangan pernah memanggil env('GEMINI_API_KEY') di controller atau service.
    | Setelah `php artisan config:cache` dijalankan saat deploy, seluruh
    | panggilan env() di luar folder config mengembalikan null tanpa galat --
    | fitur AI mati diam-diam dan penyebabnya sangat sulit dilacak.
    | Gunakan config('services.gemini.key').
    |
    */

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models',

        // Dua model, dipilih sesuai beratnya tugas.
        //
        // 'model'  -- membaca poster (Fitur 01). Tugas penglihatan dengan
        //             tulisan kecil, jadi memakai model paling mampu.
        // 'ringan' -- skor kecocokan (Fitur 04). Tugas teks sederhana:
        //             membandingkan profil dengan syarat. Model ringan sudah
        //             cukup, lebih cepat, dan hemat kuota harian.
        //
        // Versinya sengaja dipatok, bukan memakai alias 'gemini-flash-latest'.
        // Alias bisa berubah diam-diam dan membuat hasil tidak dapat diulang --
        // juri yang menjalankan proyek ini bulan depan harus mendapat perilaku
        // yang sama dengan saat dinilai.
        'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
        'ringan' => env('GEMINI_MODEL_RINGAN', 'gemini-3.5-flash-lite'),
    ],

];
