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


