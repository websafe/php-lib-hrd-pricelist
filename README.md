websafe/php-lib-hrd-pricelist
================================================================================

Draft library of PHP classes for handling HRD pricelists.



Installation
--------------------------------------------------------------------------------


~~~~ bash
git clone https://github.com/websafe/php-lib-hrd-pricelist.git
cd php-lib-hrd-pricelist
./install.sh
~~~~



Problems
--------------------------------------------------------------------------------


###  Class 'NumberFormatter' not found...

Install `libicu-devel` and the `intl` extension for PHP:

~~~~ bash
yum install libicu-devel
pecl install intl
~~~~


in `php.ini`:

~~~~ plain
extension=intl.so
~~~~