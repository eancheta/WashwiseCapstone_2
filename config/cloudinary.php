<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary URL or individual credentials
    |--------------------------------------------------------------------------
    |
    | Preferred: set CLOUDINARY_URL in your .env:
    | CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>
    |
    | The package will use 'cloud_url' if present. We still include
    | an 'upload' block for optional upload defaults.
    |
    */

    'cloud_url' => env('CLOUDINARY_URL', null),

    'url' => [
        'secure' => env('CLOUDINARY_URL_SECURE', true),
    ],

    'upload' => [
        'folder' => env('CLOUDINARY_UPLOAD_FOLDER', null),
        'preset' => env('CLOUDINARY_UPLOAD_PRESET', null),
        'notification_url' => env('CLOUDINARY_NOTIFICATION_URL', null),
    ],

];
