<?php
/**
* @var $products_left
* @var $products_right
* @var $count
* @var $id
 */

use frontend\widgets\common\Icon;
use yii\helpers\Url;

$model1 = $products_left[0];
$name = $model1 && $model1->mainConfig && $model1->mainConfig->config_name ?
    $model1->mainConfig->config_name : 'Конф.';

if($this->beginCache("product-config".$id,  ['duration' => 300])){

?>
<div class="product-list__config-inner">

    <div class="product-list__config-inner-inner">

        <div class="product-list__config__list">
            <div class="l-1">
                <?php if(!$count) {?>
                    <p class="empty">Нет продуктов</p>
                <?php } ?>
                <?php if(count($products_left)) {?>
                    <div class="product-list__config__table">
                        <div class="product-list__config__table__head">
                            <div class="plt-name" title="<?= $name ?>"><span><?= $name ?></span></div>
                            <div class="plt-article"></div>
                            <div class="plt-price">Цена</div>
                            <div class="plt-sale"></div>
                            <div class="plt-qty">Кол-во</div>
                            <div class="plt-amount"></div>
                        </div>
                        <?php foreach($products_left as $product)
                            echo $this->render('_config-item',['product'=>$product]); ?>
                    </div>
                <?php } ?>
            </div>
            <?php if(count($products_right)) {?>
            <div class="l-2">
                    <div class="product-list__config__table">
                        <div class="product-list__config__table__head">
                            <div class="plt-name" title="<?= $name ?>"><span><?= $name ?></span></div>
                            <div class="plt-article"></div>
                            <div class="plt-price">Цена</div>
                            <div class="plt-sale"></div>
                            <div class="plt-qty">Кол-во</div>
                            <div class="plt-amount"></div>
                        </div>
                        <?php foreach($products_right as $product)
                            echo $this->render('_config-item',['product'=>$product]); ?>
                    </div>
            </div>
            <?php } ?>
        </div>

        <div class="product-list__config__button">
            <?php /*
            <a class="put-link" href="#put"  title="Хочешь получать актуальную информацию о поступлении любимых товаров, а также акциях и скидках? Добавь товар в любимые!">
                <?= Icon::widget(['name' => 'like','width'=>'17px','height'=>'17px',
                    'options'=>['stroke'=>"#000",'fill'=>'none']
                ]) ?>
                <span>Любимый</span>
            </a>
            */ ?>
            <?php /* <a href="<?=Yii::$app->user->isGuest ? Url::to(['/lk/login']) : '#add-cart'?>" class="product-list-cart btn <?=Yii::$app->user->isGuest ? '' : 'js-add-cart-configs'?>">В корзину */ ?>
            <a href="#add-cart" class="product-list-cart btn js-add-cart-configs">В корзину
                <?= Icon::widget(['name' => 'cart','width'=>'20px','height'=>'20px',
                    'options'=>['fill'=>"#363636"]
                ]) ?>
            </a>
        </div>

    </div>

</div>

    </div>

    <?php
    $this->endCache();
}
?>