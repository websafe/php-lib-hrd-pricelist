#!/bin/sh

#
mkdir -p vendor/bin

#
php -n \
    -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));" \
    -- --install-dir=vendor/bin

#
php -n vendor/bin/composer.phar update