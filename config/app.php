<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nama aplikasi yang ditampilkan di notifikasi atau tampilan lainnya.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Lingkungan Aplikasi
    |--------------------------------------------------------------------------
    |
    | Menentukan lingkungan aplikasi. Atur nilainya di file ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mode Debug Aplikasi
    |--------------------------------------------------------------------------
    |
    | Menampilkan detail error saat aktif. Jika nonaktif, tampilkan error umum.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL Aplikasi
    |--------------------------------------------------------------------------
    |
    | URL dasar untuk membuat tautan melalui perintah Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Zona Waktu Aplikasi
    |--------------------------------------------------------------------------
    |
    | Zona waktu bawaan untuk fungsi tanggal dan waktu PHP.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Bahasa Aplikasi
    |--------------------------------------------------------------------------
    |
    | Menentukan bahasa bawaan untuk fitur terjemahan Laravel.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Kunci Enkripsi
    |--------------------------------------------------------------------------
    |
    | Kunci acak 32 karakter untuk mengamankan data terenkripsi.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Mode Pemeliharaan
    |--------------------------------------------------------------------------
    |
    | Menentukan driver untuk mengatur status mode pemeliharaan Laravel.
    |
    | Driver yang didukung: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
