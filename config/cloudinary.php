<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Make sure to set these in your .env file. The package will use these
    | credentials to upload your images to Cloudinary.
    |
    */

    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'secure'     => true,
    ],

    'url' => [
        'secure' => true,
    ],

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
];
