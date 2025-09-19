<?php
if (php_sapi_name() !== 'cli') {
    die("This script must be run via PHP CLI. Exiting.");
}

$phpPath = exec('which php') ?: (file_exists('/vercel/path0/php') ? '/vercel/path0/php' : '/usr/bin/php');
if (!file_exists($phpPath)) {
    file_put_contents('php-error.log', "PHP executable not found at $phpPath\n", FILE_APPEND);
    die("PHP executable not found. Check logs.");
}

// Install Composer dependencies
file_put_contents('/tmp/composer-setup.php', file_get_contents('https://getcomposer.org/installer'));
exec("$phpPath /tmp/composer-setup.php --install-dir=/tmp --filename=composer");
exec("mv /tmp/composer /usr/local/bin/composer");
exec("$phpPath /usr/local/bin/composer install --optimize-autoloader --no-dev");

// Install Node.js dependencies and build Vite
exec('npm install --omit=optional'); // Skip optional Linux binaries
$viteOutput = [];
$viteReturn = 0;
exec('npm run build', $viteOutput, $viteReturn);
if ($viteReturn !== 0) {
    file_put_contents('vite-error.log', "Vite build failed: " . implode("\n", $viteOutput) . "\n", FILE_APPEND);
    die("Vite build failed. Check vite-error.log.");
}

// Verify manifest
$manifestPath = 'public/build/manifest.json';
if (!file_exists($manifestPath)) {
    file_put_contents('vite-error.log', "Manifest not found at $manifestPath after build.\n", FILE_APPEND);
    die("Manifest not generated. Check vite-error.log.");
}

echo "Build completed successfully.\n";
?>
