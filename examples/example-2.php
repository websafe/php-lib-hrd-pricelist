<?php
//
chdir(__DIR__ . '/../');
//
require_once 'vendor/autoload.php';
//
$cennik = new Websafe\Hrd\Pricelist\DomainPricelist('temp/cennik-hrd.html');
//
$conditionCounter = array('OK' => 0, 'WARNING' => 0, 'CRITICAL' => 0);
?>

<?php require 'example-header.php';?>

<p>Niniejszy przykład sprawdza wszystkie ceny z cennika, porównując 
cenę zakupu z ceną sprzedaży.
    <ul>
        <li>Jeśli cena zakupu jest niższa od ceny sprzedaży, status 
            <span class="OK">OK</span>.
        </li>
        <li>Jeśli cena sprzedaży jest równa cenie zakupu - status 
            <span class="WARNING">WARNING</span>.
        </li>
        <li>Jeśli cena sprzedaży jest niższa od ceny zakupu - status 
            <span class="CRITICAL">CRITICAL</span>.
        </li>
    </ul>
</p>


<pre>

<?php
foreach($cennik->getItemsIndex() as $index)
{
    $item = $cennik->getItemByIndex($index);

    foreach($cennik->getPriceTypes() as $priceType)
    {
        $purchasePrice = $item->getPurchasePrice($priceType);
        $sellPrice = $item->getSellPrice($priceType);
        //
        if(null !== $purchasePrice)
        {
            //
            if($purchasePrice < $sellPrice)
            {
                $priceTypeCondition = 'OK';
                $conditionCounter[$priceTypeCondition]++;
            }
            elseif($purchasePrice == $sellPrice)
            {
                $priceTypeCondition = 'WARNING';
                $conditionCounter[$priceTypeCondition]++;
            }
            elseif($purchasePrice > $sellPrice)
            {
                $priceTypeCondition = 'CRITICAL'; 
                $conditionCounter[$priceTypeCondition]++;
            }

            echo '<b><var>' . $item->getType() . '</var></b>'
                . ' price condition for <var>' . $priceType . '</var> is ' 
                . '<span class="' .  $priceTypeCondition . '">'
                . $priceTypeCondition
                . '</span>' . PHP_EOL;
        }
    }
    echo PHP_EOL;
}
?>

</pre>


<?php require 'example-footer.php';?>