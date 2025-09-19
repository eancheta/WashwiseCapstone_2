<?php
// Find PHP executable
$phpPath = exec('which php') ?: '/usr/bin/php'; // Fallback to a common path
if (!file_exists($phpPath)) {
    die("PHP executable not found at $phpPath");
}

// Download and install Composer
exec("$phpPath -r \"copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');\"");
exec("$phpPath /tmp/composer-setup.php --install-dir=/tmp --filename=composer");
exec("mv /tmp/composer /usr/local/bin/composer");

// Install PHP dependencies
exec("$phpPath /usr/local/bin/composer install --optimize-autoloader --no-dev");

// Install Node.js dependencies and build assets
exec('npm install');
exec('npm run build');
?>
