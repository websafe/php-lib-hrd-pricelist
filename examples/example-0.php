<?php
//
chdir(__DIR__ . '/../');
//
require_once 'vendor/autoload.php';
//
$cennik = new Websafe\Hrd\Pricelist\DomainPricelist('temp/cennik-hrd.html');
?>

<?php require 'example-header.php';?>

<table border="1">
    <thead>
        <tr>
            <th>Typ</th>
            <th>ID</th>

<?php foreach($cennik->getPriceTypes() as $priceType):?>
            <th colspan="2"><?php echo $priceType;?></th>
<?php endforeach;?>

        </tr>
    </thead>
    <tbody>

<?php if($cennik->getItemsCount() > 0):?>
        
    <?php foreach($cennik->getItemsIndex() as $index): ?>
    <?php $item = $cennik->getItemByIndex($index); ?>
        <tr>
            <td>&nbsp;<?php echo $item->getType();?></td>
            <td>&nbsp;<?php echo $item->getId();?></td>

        <?php foreach($cennik->getPriceTypes() as $priceType):?>

            <td>&nbsp;<?php echo $item->getPurchasePrice($priceType);?></td>
            <td>&nbsp;<?php echo $item->getSellPrice($priceType);?></td>

        <?php endforeach;?>

        </tr>

    <?php endforeach;?>

<?php else: ?>

        <tr>
            <td colspan="<?php echo ((count($cennik->getPriceTypes())*2)+1);?>">
                <p>Brak danych. Czy zapisałeś pobrany plik HTML z cennikiem
                w katalogu `temp/` pod nazwą `cennik-hrd.html`?</p>
            </td>
        </tr>
        
<?php endif; ?>

    </tbody>
</table>

<?php require 'example-footer.php';?>