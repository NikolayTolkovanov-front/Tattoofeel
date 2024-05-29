<?php
use common\models\Currency;
use common\models\ProductPrice;
use frontend\widgets\common\Icon;
use yii\helpers\Url;
?>

<div class="lk-table__row">
    <div class="lk-table-fd-name">
        <a href="<?= Url::to($lp->product->route)?>" class="lk-table-card">
            <span style="background-image:url(<?= Url::to($lp->product->getImgUrl())?>)"></span>
            <h2 class="lk-table-card__head">
                <?=mb_strimwidth($lp->product->title, 0, 75, "...")?>
                <span><?= $lp->product->article ? 'Арт. '.$lp->product->article : '' ?></span>
                <span></span>
            </h2>
        </a>
    </div>

    <div class="lk-table-fd-qty">
        <?php /*<div class="number _flex _avg js-lk-change-cart" data-product-id="<?= $lp->product->id ?>" data-max="<?= $lp->product->amount ?>"> */ ?>
        <div class="number _flex _avg js-lk-change-order" data-product-id="<?= $lp->product->id ?>" data-max="<?= $lp->product->amount ?>">
            <span class="number__minus">-</span>
            <input type="number" class="number__value" value="<?=$lp->count?>" step="1" min="1">
            <?php /*
            <span class="number__value"><?= ($lp->count > 0 && $lp->count < 10 ? '0':'').$lp->count ?></span>
            */?>
            <input type="hidden" value="<?= $lp->count ?>">
            <span class="number__plus">+</span>
        </div>
    </div>

    <div class="lk-table-fd-sum" data-price="<?=($lp->is_gift ? 0 : $lp->product->clientPriceValue) / 100?>">
        <?php $pp = ProductPrice::getParsePrice(
            $lp->is_gift ? 0 : $lp->product->clientPriceValue,
            Currency::DEFAULT_CART_PRICE_CUR_ISO,
            $to = null,
            $addSale = 0,
            (int) $lp->count
        ); echo $pp->ceil_fr.' '.$pp->cur ?>
    </div>

    <?php /* <div class="lk-table-fd-def js-lk-cart-deferred " data-product-id="<?=$lp->product->id?>">
        <a class="link-def lk-table-fd-def__put" href="#put" title="Хочешь получать актуальную информацию о поступлении любимых товаров, а также акциях и скидках? Добавь товар в любимые!">
            <svg class="icon" width="17px" height="17px" stroke="#000" fill="none"><use xlink:href="/img/svg/icons.svg#like"></use></svg>
        </a>
        <a class="link-def lk-table-fd-def__unput" href="#unput">
            <svg class="icon" width="17px" height="17px" stroke="#000" fill="#000"><use xlink:href="/img/svg/icons.svg#like"></use></svg>
        </a>
    </div> */ ?>

    <div class="lk-table-fd-del">
        <a class="link-del js-lk-order-del" href="#remove" data-product-id="<?=$lp->product->id?>">Удалить</a>
    </div>
</div>
