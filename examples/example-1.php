<?php
//
chdir(__DIR__ . '/../');
//
require_once 'vendor/autoload.php';
//
$cennik = new Websafe\Hrd\Pricelist\DomainPricelist('temp/cennik-hrd.html');
?>

<?php require 'example-header.php';?>

<pre>
    <?php print_r($cennik);?>
</pre>

<?php require 'example-footer.php';?>