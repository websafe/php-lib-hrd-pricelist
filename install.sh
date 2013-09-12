#!/bin/sh

#
mkdir -p vendor/bin

#
curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=vendor/bin

#
vendor/bin/composer.phar update
