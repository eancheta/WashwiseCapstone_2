<?php
// Self-execution check
if (php_sapi_name() !== 'cli') {
    die("This script must be run via PHP CLI. Exiting.");
}

// Find PHP executable dynamically
$phpPath = exec('which php') ?: (file_exists('/vercel/path0/php') ? '/vercel/path0/php' : '/usr/bin/php');
if (!file_exists($phpPath)) {
    file_put_contents('php-error.log', "PHP executable not found at $phpPath\n", FILE_APPEND);
    die("PHP executable not found. Check logs.");
}

// Download and install Composer
file_put_contents('/tmp/composer-setup.php', file_get_contents('https://getcomposer.org/installer'));
exec("$phpPath /tmp/composer-setup.php --install-dir=/tmp --filename=composer");
exec("mv /tmp/composer /usr/local/bin/composer");

// Install PHP dependencies
exec("$phpPath /usr/local/bin/composer install --optimize-autoloader --no-dev");

// Install Node.js dependencies and build assets
exec('npm install');
exec('npm run build');

echo "Build completed successfully.\n";
?>
