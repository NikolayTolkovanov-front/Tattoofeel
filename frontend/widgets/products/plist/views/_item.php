<?php
/**
 * @var $model mixed
 */

use frontend\widgets\common\Icon;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="product-list__item model-is-discount-<?=($model->is_discount || $model->is_super_price ? '1' : '0')?>" data-id="<?= $model->id ?>" itemscope="" itemtype="http://schema.org/Product">

    <?php if( $model->clientSalePercent ) {?>
        <span class="product-list__item__sale">-<?= $model->clientSalePercent ?>%</span>
    <?php } ?>

    <?php if (!$model->amount) {?>
        <span class="product-row-list__item__out_of_stock">нет в наличии</span>
    <?php } ?>

    <a href="<?= Url::to([$model->route]) ?>" title="<?= Html::encode($model->title) ?>" class="product-list__item__top-a" data-pjax="0">
        <span class="product-list__item__img" style="background-image: url('<?=$model->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($model->getImgUrl())?>'); "></span>
        <h2 class="product-list__item__head"><span itemprop="name"><?= $model->getTitleShort() ?></span></h2>
    </a>

        <span class="product-list__item__art">
            <?= $model->subTitle ?>
        </span>

    <span class="product-list__item__price-box" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
        <span>
            <?php /*if( $model->clientSalePercent ) {*/ ?>
            <?php if ($model->retailPrice->price > $model->clientPriceValue) {?>
                <span class="product-list__item__price-old"><?= $model->getFrontendOldPrice() ?></span>
            <?php } ?>
            <span class="product-list__item__price"><?= $model->getFrontendCurrentPrice() ?></span>

            <meta itemprop="price" content="<?= number_format((int)$model->clientPriceValue / 100, 2, '.', '') ?>">
            <meta itemprop="priceCurrency" content="RUB">
        </span>
    </span>

    <?php /*if(!empty($model->amount)) { ?>
    <a class="product-list-cart btn pCart js-add-cart" data-product-id="<?= $model->id ?>" href="#add-cart" data-amount="<?= $model->amount ?>" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
        <span class="product-list-cart__in">
            <span>В корзину</span>
            <span class="product-list-cart__in__btn">
                <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                    'options' => ['fill'=>"#363636", 'class' => 'icon'],
                ]) ?>
            </span>
        </span>
        <link itemprop="availability" href="http://schema.org/InStock">
    </a>
    <?php } else { ?>
        <a class="product-list-cart pCart" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
            <span class="__empty">нет в наличии</span>
            <link itemprop="availability" href="http://schema.org/OutOfStock">
        </a>
    <?php } */ ?>

    <a class="product-list-cart btn pCart <?=Yii::$app->user->isGuest ? '' : 'js-add-cart'?>" data-product-id="<?= $model->id ?>" href="<?=Yii::$app->user->isGuest ? Url::to(['/lk/login']) : '#add-cart'?>" data-amount="<?= $model->amount ?>" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
        <span class="product-list-cart__in">
            <span>В корзину</span>
            <span class="product-list-cart__in__btn">
                <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                    'options' => ['fill'=>"#363636", 'class' => 'icon'],
                ]) ?>
            </span>
        </span>
        <link itemprop="availability" href="http://schema.org/InStock">
    </a>
</div>
