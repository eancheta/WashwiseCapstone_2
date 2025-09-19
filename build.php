<?php
// Download and install Composer
exec('curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php');
exec('/usr/local/bin/php /tmp/composer-setup.php --install-dir=/tmp --filename=composer');
exec('mv /tmp/composer /usr/local/bin/composer');
// Install PHP dependencies
exec('/usr/local/bin/composer install --optimize-autoloader --no-dev');
// Install Node.js dependencies and build assets
exec('npm install');
exec('npm run build');
?>
