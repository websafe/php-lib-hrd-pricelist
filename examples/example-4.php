<?php
//
chdir(__DIR__ . '/../');
//
require_once 'vendor/autoload.php';
//
$cennik = new Websafe\Hrd\Pricelist\DomainPricelist('temp/cennik-hrd.html');
?>

<?php require 'example-header.php';?>

<p>Przykładowy cennik na stronę internetową - pełna lista domen.</p>

<table border="1">
    <thead>
        <tr>
            <th rowspan="2">Domena</th>
            <th colspan="2">Cena</th>
        </tr>
        <tr>
            <th>Rejestracja</th>
            <th>Odnowienie</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach($cennik->getItemsIndex() as $index): ?>
    <?php $item = $cennik->getItemByIndex($index); ?>
        <tr>
            <td>&nbsp;<?php echo $item->getType();?></td>
            <td class="price">
                <?php echo number_format($item->getSellPrice('value_register'), 2, ',', '.');?>
            </td>
            <td class="price">
                <?php echo number_format($item->getSellPrice('value_renew'), 2, ',', '.');?>
            </td>
        </tr>

    <?php endforeach;?>

    </tbody>
</table>


<?php require 'example-footer.php';?>