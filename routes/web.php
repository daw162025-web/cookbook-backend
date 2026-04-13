<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-cloudinary', function () {
    return [
        'check_url' => env('CLOUDINARY_URL') ? 'SI, LA VEO' : 'NO, ES NULL',
        'check_name' => env('CLOUDINARY_CLOUD_NAME') ? 'SI, LO VEO' : 'NO, ES NULL'
    ];
});

Route::get('/ejecutar-seed', function () {
    try {
        Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);
        return "¡Base de datos reseteada y cargada con éxito!";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
