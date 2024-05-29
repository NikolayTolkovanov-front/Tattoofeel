<?php
/**
 * @var $model mixed
 * @var $isConfigInStockByMsIds array
 * @var $mappedRetailPrices array
 */

use frontend\widgets\common\Icon;
use frontend\helpers\Debug;
use yii\helpers\Url;
use yii\helpers\Html;

if ($this->beginCache("product-item-config".$model->id,  ['duration' => 300])){
    Debug::logModel($model);
?>
<div class="product-list__item _has-config model-is-discount-<?=$model->is_discount?>" data-id="<?= $model->id ?>">
    <?php if (!$isConfigInStockByMsIds[$model->config_ms_id]) {?>
        <span class="product-row-list__item__out_of_stock">нет в наличии</span>
    <?php } ?>

    <a href="<?= Url::to([$model->route]) ?>" title="<?= Html::encode($model->categoryConfig->title) ?>" class="product-list__item__top-a" data-pjax="0">
        <span class="product-list__item__img" style="background-image: url('<?=$model->imgUrl?>'); background-image: url('<?=  \common\components\Common::ImageReplace($model->imgUrl )?>'); "></span>
        <h2 class="product-list__item__head"><span><?= $model->categoryConfig->titleShort ?></span></h2>
    </a>

        <span class="product-list__item__art">
            <?= $model->subTitle ?>
        </span>

    <span class="product-list__item__price-box" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
        <span>
            <?php if ($mappedRetailPrices[$model->id] && $mappedRetailPrices[$model->id]->price > $model->clientPriceValue) {?>
                <span class="product-list__item__price-old"><?= $model->getFrontendOldPrice() ?></span>
            <?php } ?>
            <span class="product-list__item__price"><?=$model->frontendMinPriceValueWithoutOutOfStock?></span>
        </span>

        <meta itemprop="price" content="<?= number_format((int)$model->clientPriceValue / 100, 2, '.', '') ?>">
        <meta itemprop="priceCurrency" content="RUB">
    </span>

    <a class="product-list-cart btn pArw">
        <span class="product-list-cart__in">
            <span>Модификации</span>
            <span class="product-list-cart__in__btn">
                <?= Icon::widget(['name' => 'arw','width'=>'12px','height'=>'8px',
                    'options' => ['stroke'=>"#363636", 'class' => 'icon i-a'],
                ]) ?>
                <?= Icon::widget(['name' => 'loader','width'=>'12px','height'=>'8px',
                    'options' => ['stroke'=>"#363636", 'class' => 'icon i-l'],
                ]) ?>
            </span>
        </span>
    </a>

</div>
<?php
    $this->endCache();
} else
{
    Debug::logModel($model, "cached");
}
?>