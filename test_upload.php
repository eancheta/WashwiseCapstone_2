<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

// Path to a real image in your public folder
$filePath = __DIR__ . '/public/apple-touch-icon.png';

try {
    $result = Cloudinary::upload($filePath, [
        'folder' => 'test_uploads', // folder on Cloudinary
    ]);

    echo "Upload successful! URL: " . $result->getSecurePath() . PHP_EOL;
} catch (\Throwable $e) {
    echo "Upload failed: " . $e->getMessage() . PHP_EOL;
}
