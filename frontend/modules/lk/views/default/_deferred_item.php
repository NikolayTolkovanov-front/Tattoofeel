<?php

use yii\helpers\Url;
use frontend\widgets\common\Icon; ?>

<div class="lk-table__row">
    <div class="lk-table-fd-name">
        <a href="<?= Url::to($model->route) ?>" class="lk-table-card">
            <span style="background-image:url(<?= $model->getImgUrl() ?>)"></span>
            <h2 class="lk-table-card__head">
                <?=mb_strimwidth($model->title, 0, 75, "...")?>
                <span><?= $model->article ? 'Арт. '.$model->article : '' ?></span>
            </h2>
        </a>
    </div>
    <div class="lk-table-fd-stock">
        <?php if($model->amountIndex > 0) {?>
        <span class="in-stock">
            <?= Icon::widget(['name' => 'stock-yes','width'=>'24px','height'=>'24px',
                'options'=>['fill'=>"#F8CD4F", 'stroke'=>"#F8CD4F"]]) ?>
            Есть в наличии
        </span>
        <?php } else { ?>
        <span class="in-stock">
            <?= Icon::widget(['name' => 'stock-no','width'=>'24px','height'=>'24px',
                'options'=>['fill'=>"#D3D3D3", 'stroke'=>"#D3D3D3"]]) ?>
            Нет в наличии
        </span>
        <?php } ?>
    </div>
    <div class="lk-table-fd-buy">
        <?php /* <a class="link-def <?=Yii::$app->user->isGuest ? '' : 'js-add-cart'?>" href="<?=Yii::$app->user->isGuest ? Url::to(['/lk/login']) : '#buy'?>" data-product-id="<?= $model->id ?>" data-amount="<?= $model->amount ?>"> */ ?>
        <a class="link-def js-add-cart" href="#buy" data-product-id="<?= $model->id ?>" data-amount="<?= $model->amount ?>">
            <?= Icon::widget(['name' => 'cart','width'=>'24px','height'=>'24px','options'=>['fill'=>"#363636"]]) ?>
            <span>Купить</span>
        </a>
    </div>
    <div class="lk-table-fd-del">
        <a class="link-del js-lk-del-deferred" href="#unput" data-product-id="<?= $model->id ?>">Удалить</a>
    </div>
</div>
