<?php
/**
 * @var $model mixed
 */
use yii\helpers\Url;

?>
<article class="articles__item">
    <a class="articles__item__pict" href="<?= Url::to(['/'.Yii::$app->controller->id.'/'.$model->slug]) ?>">
        <div style="background-image:url(<?= $model->getImgUrl() ?>)"></div>
    </a>
    <div class="articles__item__desc">
        <div class="articles__item__desc__head">
            <h2 class="h3">
                <a href="<?= Url::to(['/'.Yii::$app->controller->id.'/'.$model->slug]) ?>">
                    <?= $model->title ?>
                </a>
            </h2>
            <?php if($model->published_at) {?>
                <span><?= Yii::$app->formatter->asDate($model->published_at) ?></span>
            <?php } ?>
        </div>
        <div class="block-typo"><?= $model->body_short ?></div>
    </div>
</article>
<div class="articles__item__more">
    <a class="btn _gray" href="<?= Url::to(['/'.Yii::$app->controller->id.'/'.$model->slug]) ?>">
        Читать полностью
    </a>
</div>
