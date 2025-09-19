#!/usr/bin/env sh
# Download and install Composer
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
/usr/local/bin/php /tmp/composer-setup.php --install-dir=/tmp --filename=composer
mv /tmp/composer /usr/local/bin/composer
# Install PHP dependencies
/usr/local/bin/composer install --optimize-autoloader --no-dev
# Install Node.js dependencies and build assets
npm install
npm run build
