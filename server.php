<?php
// server.php — DEV ONLY
// Usage: php -S 127.0.0.1:8000 server.php
// This router forces Laravel to handle requests under /build/* (so middleware/route proxy can add headers).
// For other static files, let PHP built-in server serve them normally.

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/public' . $uri;

// If the requested file exists AND it's NOT under /build/, let the built-in server serve it directly
if ($uri !== '/' && file_exists($publicPath) && !str_starts_with($uri, '/build/')) {
    return false; // serve the static file
}

// Otherwise pass it to Laravel's front controller
require_once __DIR__ . '/public/index.php';
