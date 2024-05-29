<?php use frontend\widgets\common\Icon;
use yii\helpers\Url; ?>
<?php /* remember config js change */?>

<?php
$currentPrice = $model->clientPriceValue;
$oldPrice = $model->retailPrice->price;
$salePercent = $oldPrice ? number_format(floor(100 - (100 * $currentPrice / $oldPrice)), 0, '.', ' ') : 0;

//echo "<pre>";print_r($model->retailPrice->price);echo "</pre>";
////echo "<pre>";print_r($model);echo "</pre>";
//echo "<pre>";print_r($model->clientOldPriceValue);echo "</pre>";
//echo "<pre>";print_r($model->clientSalePercent);echo "</pre>";
?>

<div class="product-card-data__price">
    <div class="product-card-price">
        <div class="product-card-price__left">
            <div class="product-card-prices">
                <span class="product-card-price__old<?= !$salePercent ? ' none' : ''?>">
                        <span
                                class="product-card-price__old__num"
                                id="pr-old-price"
                        ><?= $model->getFrontendOldPrice() ?></span>
                        <span class="product-card-price__old__sale">
                            -<span id="pr-sale"><?= $salePercent ?></span>%
                        </span>
                    </span>
                <span class="product-card-price__current" id="pr-price">
                    <?= $model->getFrontendCurrentPrice() ?>
                </span>
            </div>

            <div class="product-card-data__count">
                <div class="number _big _flex" data-product-id="<?= $model->id ?>" data-min="1" data-max="<?= $model->amount ?>">
                    <span class="number__minus">-</span>
                        <input type="number" class="number__value" value="1" step="1" min="1">
                        <?php /*
                        <span class="number__value">01</span> */ ?><input type="hidden" value="1" />
                    <span class="number__plus">+</span>
                </div>
            </div>
        </div>

        <div class="product-card-price__right">
            <div class="product-btn-card">
                <?php /* <a class="btn <?=Yii::$app->user->isGuest ? '' : 'js-add-cart'?>" href="<?=Yii::$app->user->isGuest ? Url::to(['/lk/login']) : '#add-cart'?>" data-product-id="<?= $model->id ?>" data-count="1" id="add-cart-product" data-amount="<?= $model->amount ?>"> */ ?>
                <a class="btn js-add-cart" href="#add-cart" data-product-id="<?= $model->id ?>" data-count="1" id="add-cart-product" data-amount="<?= $model->amount ?>">
                    В корзину
                    <?= Icon::widget(['name' => 'cart','width'=>'24px','height'=>'24px',
                        'options'=>['fill'=>"#363636"]
                    ]) ?>
                </a>
            </div>
            <div class="product-btn-click">
                <a class="btn _black js-show-buy-one-click-form" href="#" data-product-id="<?= $model->id ?>" onclick="ym(73251517,'reachGoal','one_click_button'); return true;">
                    Купить в один клик
                </a>
            </div>
            <div class="product-btn-put">
                <div class="put-link-el <?= Yii::$app->client->identity->isDeferred($model->id) ? '-act' : '' ?>" data-product-id="<?= $model->id ?>">
                    <a class="put-link"
                       href="#put"
                       title="Хочешь получать актуальную информацию о поступлении любимых товаров, а также акциях и скидках? Добавь товар в любимые!"
                       onclick="ym(73251517,'reachGoal','add_favorite_detail'); return true;"
                    >
                        <?= Icon::widget(['name' => 'like','width'=>'17px','height'=>'17px',
                            'options'=>['stroke'=>"#000",'fill'=>'none']
                        ]) ?>
                        <span>Любимый</span>
                    </a>
                    <a class="un-put-link" href="#un-put">
                        <?= Icon::widget(['name' => 'like','width'=>'17px','height'=>'17px',
                            'options'=>['stroke'=>"#000",'fill'=>'#000']
                        ]) ?>
                        <span>Удалить из любимых</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <meta itemprop="price" content="<?= number_format((int)$model->clientPriceValue / 100, 2, '.', '') ?>">
    <meta itemprop="priceCurrency" content="RUB">
</div>

<div class="my-modal">
    <div id="buy-one-click-form" class="modal-form"></div>
</div>

<?php
$response = array(
    'ecommerce' => array(
        'currencyCode' => 'RUB',
    ),
);

$response['ecommerce']['detail']['products'][] = array(
    'id' => $model->id,
    'name' => $model->title,
    'price' => $model->clientPriceValue / 100,
    'brand' => $model->brand_->title ?: '',
    'category' => $model->category->title,
);
?>

<script>
    window.dataLayer.push(<?=json_encode($response, JSON_UNESCAPED_UNICODE)?>);

    $("body").on('click', '.product-btn-click', function(){
        $(".one_click-modal").show();
    });
</script>
