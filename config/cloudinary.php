<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary config
    |--------------------------------------------------------------------------
    |
    | Provide CLOUDINARY_URL or the individual parts via env. This file
    | simply exposes the env values to the app in a stable way so your
    | editor / code can safely reference config('cloudinary.xxx').
    |
    */

    // Full connection string (cloudinary://API_KEY:API_SECRET@CLOUD_NAME)
    'cloud_url'    => env('CLOUDINARY_URL', null),

    // Individual pieces (optional, may be null if using CLOUDINARY_URL)
    'cloud_name'   => env('CLOUDINARY_CLOUD_NAME', null),
    'api_key'      => env('CLOUDINARY_API_KEY', null),
    'api_secret'   => env('CLOUDINARY_API_SECRET', null),

    // Optional upload preset name (if you use one)
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', null),

    // Optional: notification / webhook url for upload notifications
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL', null),

];
