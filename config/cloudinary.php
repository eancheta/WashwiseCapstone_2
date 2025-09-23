<?php

return [

    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    'url' => [
        'secure' => true,
    ],

    'upload' => [
        'preset'           => env('CLOUDINARY_UPLOAD_PRESET', null),
        'folder'           => env('CLOUDINARY_UPLOAD_FOLDER', null),
        'notification_url' => env('CLOUDINARY_NOTIFICATION_URL', null),
    ],

];
