<?php
use yii\helpers\Html;
use frontend\widgets\common\Icon;

$currentPrice = $product->clientPriceValue;
$oldPrice = $product->retailPrice->price;
$salePercent = $oldPrice ? number_format(floor(100 - (100 * $currentPrice / $oldPrice)), 0, '.', ' ') : 0;


if($this->beginCache("config-item".$product->id,  ['duration' => 300])){
?>

<div class="plt-config <?= (empty($product->amount) || $product->amount < 0) ? '__empty' : '' ?>" data-product-id="<?= $product->id ?>" data-count="0">
    <div class="plt-name" title="<?= $product->title ?>">
        <span>
            <?= $product->titleShort ?>
            <span class="plt-name__art"><?= $product->article ?></span>
        </span>
    </div>

    <div class="plt-article"><?= $product->article ?></div>

    <div class="plt-price">
        <?php if ($salePercent) {?>
            <span class="plt-price-old"><?= $product->getFrontendOldPrice() ?></span>
        <?php } ?>
        <?=$product->frontendCurrentPrice?>
    </div>

    <?php /*<div class="plt-sale"><?= $product->clientSalePercent ? "-{$product->clientSalePercent}%" : '' ?></div>*/ ?>
    <div class="plt-sale"><?= $salePercent ? "-{$salePercent}%" : '' ?></div>

    <div class="plt-qty">
        <?php if (!(empty($product->amount) || $product->amount < 0)):?>
            <div class="number" data-min="0" data-max="<?= $product->amount ?>">
                <span class="number__minus">-</span>
                <input type="number" class="number__value" value="0" step="1" min="1">
                <input type="hidden" value="0" />
                <span class="number__plus">+</span>
            </div>
        <?php else:?>
            <div class="number">Нет в наличии</div>
        <?php endif;?>

        <div class="put-link-el <?= Yii::$app->client->identity->isDeferred($product->id) ? '-act' : '' ?>" data-product-id="<?= $product->id ?>">
            <a class="put-link"
               href="#put"
               title="Хочешь получать актуальную информацию о поступлении любимых товаров, а также акциях и скидках? Добавь товар в любимые!"
                <?php if ($salePercent) echo 'style="top:10px;"';?>
               onclick="ym(73251517,'reachGoal','add_favorite_list'); return true;"
            >
                <?= Icon::widget(['name' => 'like','width'=>'13px','height'=>'13px',
                    'options'=>['stroke'=>"#000",'fill'=>'none']
                ]) ?>
            </a>
            <a class="un-put-link" href="#un-put" <?php if ($salePercent) echo 'style="top:10px;"';?>>
                <?= Icon::widget(['name' => 'like','width'=>'13px','height'=>'13px',
                    'options'=>['stroke'=>"#000",'fill'=>'#000']
                ]) ?>
            </a>
        </div>
    </div>

    <?php /*
    <div class="plt-amount">
        <div title="<?= Html::encode($product->amountTitle) ?>"
             class="product-card-amount _v_<?= $product->amountIndex ?>"></div>
    </div>
    */ ?>
</div>

    <?php
    $this->endCache();
}
?>