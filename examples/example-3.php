<?php
//
chdir(__DIR__ . '/../');
//
require_once 'vendor/autoload.php';
//
$cennik = new Websafe\Hrd\Pricelist\DomainPricelist('temp/cennik-hrd.html');
?>

<?php require 'example-header.php';?>

<p>Przykład tekstowej tabelki ;-)</p>


<pre>

<?php
//
echo str_pad('# ',  6, ' ', STR_PAD_LEFT);
echo ' | ';
echo str_pad('Typ',  20);
echo ' | ';
echo str_pad('ID',  5, ' ', STR_PAD_LEFT);
foreach ($cennik->getPriceTypes() as $priceType)
{
    echo ' | ' . str_pad($priceType,  25, ' ', STR_PAD_LEFT);
}
echo PHP_EOL;
//
//
    //
    echo '=======';
    echo '+';
    echo '======================';
    echo '+';
    echo '=======';
    echo '+';
    //
    foreach($cennik->getPriceTypes() as $priceType)
    {
        //
        echo '=============';
        echo '+';
        echo '=============';
        echo '+';
    }
    echo PHP_EOL;

//
$counter = 0;
//
foreach($cennik->getItemsIndex() as $index)
{
    $counter++;
    $item = $cennik->getItemByIndex($index);
    //
    echo str_pad($counter . '.', 6, ' ', STR_PAD_LEFT);
    echo ' | ';
    echo str_pad($item->getType(), 20);
    echo ' | ';
    echo str_pad($item->getId(), 5, ' ', STR_PAD_LEFT);
    //
    foreach($cennik->getPriceTypes() as $priceType)
    {
        $purchasePrice = $item->getPurchasePrice($priceType);
        $sellPrice = $item->getSellPrice($priceType);
        //
        echo ' | ';
        echo str_pad($purchasePrice, 11, ' ', STR_PAD_LEFT);
        echo ' | ';
        echo str_pad($sellPrice, 11, ' ', STR_PAD_LEFT);
    }
    echo PHP_EOL;
    //
    echo '-------';
    echo '+';
    echo '----------------------';
    echo '+';
    echo '-------';
    echo '+';
    //
    foreach($cennik->getPriceTypes() as $priceType)
    {
        //
        echo '-------------';
        echo '+';
        echo '-------------';
        echo '+';
    }
    echo PHP_EOL;
}
?>

</pre>


<?php require 'example-footer.php';?>