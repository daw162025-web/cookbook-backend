<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    */

    'cloud_url' => env('CLOUDINARY_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'ml_default'),

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
];
