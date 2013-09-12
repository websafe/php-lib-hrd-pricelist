#!/bin/sh

#
mkdir -p vendor/bin

#
php \
    -n \
    -d allow_url_fopen=yes \
    -d suhosin.executor.include.whitelist=phar \
    -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));" \
    -- --install-dir=vendor/bin

#
php \
    -n \
    -d allow_url_fopen=yes \
    -d suhosin.executor.include.whitelist=phar \
    vendor/bin/composer.phar update \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader
