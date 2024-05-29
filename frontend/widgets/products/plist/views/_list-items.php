<?php
/* @var $dataProvider yii\data\ActiveDataProvider*/
/* @var $emptyListShow bool */
/* @var $models Product[] */
/* @var $isConfigInStockByMsIds array */
/* @var $mappedRetailPrices array */

use common\models\Product;

?>

<?php if ($emptyListShow && empty($models) ){ ?>
    <p class="empty">Товаров не найдено.</p>
<?php } else {
    foreach($models as $model) {
        echo $this->render('_item-is-config',
            [
                'model' => $model,
                'isConfigInStockByMsIds' => $isConfigInStockByMsIds,
                'mappedRetailPrices' => $mappedRetailPrices,
            ]
        );
    }
    if ($dataProvider->getTotalCount() <= ($dataProvider->pagination->page + 1) * $dataProvider->pagination->pageSize):?>
        <div class="none last-page"></div>
    <?php endif;?>
<?php } ?>

