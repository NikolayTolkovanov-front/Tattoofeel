<?php

/**
* @var $model
 * @var $isConfigInStock mixed
 * @var $retailPrices ProductPrice
 *
 */

use common\models\ProductPrice;
use frontend\widgets\common\Icon;
use yii\helpers\Url;
use yii\helpers\Html;

?>

<div class="product-row-list__item i-<?=$model->id?>">
    <?php if (!$isConfigInStock) {?>
        <span class="product-row-list__item__out_of_stock">нет в наличии</span>
    <?php } ?>

    <a href="<?= Url::to([$model->route]) ?>" title="<?= Html::encode($model->categoryConfig->title) ?>" class="product-row-list__item__top-a" data-pjax="0">
        <span class="product-row-list__item__img" style="background-image: url('<?=$model->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($model->getImgUrl())?>'); "></span>
        <h3 class="product-row-list__item__head"><span><?= $model->categoryConfig->titleShort ?></span></h3>
    </a>
    <span class="product-row-list__item__art"><?= $model->subTitle ?></span>
    <span class="product-row-list__item__price-box"><span>
        <?php if ($retailPrices->price > $model->clientPriceValue) {?>
            <span class="product-row-list__item__price-old"><?= $model->getFrontendOldPrice() ?></span>
        <?php } ?>
        <span class="product-row-list__item__price"><?=$model->frontendMinPriceValueWithoutOutOfStock?></span>
    </span></span>
    <a href="<?= Url::to([$model->route]) ?>" class="product-row-list__item__cart btn">В корзину
        <?= Icon::widget(['name' => 'cart','width'=>'32px','height'=>'32px',
            'options'=>['fill'=>"#363636"]
        ]) ?>
    </a>
</div>