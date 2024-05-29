<?php
/**
* @var $model
 */
?>
<div class="home-team">
    <div class="home-team__head">
        <?php if($model->url) { ?>
            <h2 class="h1"><a href="<?= $model->url ?>"><?= $model->title ?></a></h2>
        <?php } else { ?>
            <h2 class="h1"><?= $model->title ?></h2>
        <?php } ?>
    </div>
    <div class="home-team__grid">
        <div class="home-team__grid__desc">
            <div class="home-team__grid__desc__numbers">
                <span><span><?= $model->custom_1 ?></span> лет на рынке</span>
                <span><span><?= $model->custom_2 ?></span> онлайн покупок</span>
                <span><span><?= $model->custom_3 ?></span> довольных отзывов<br />в нашей <a href="#">группе Вк</a></span>
            </div>
            <div class="home-team__grid__desc__text">
                <?= $model->body_short ?>
            </div>
            <div class="home-team__grid__desc__text__more">
                <a href="<?= $model->url ?>" class="btn _gray">Читать полностью</a>
            </div>
        </div>
        <div class="home-team__grid__photo">
            <div>
                <div style="background-image: url('<?=$model->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($model->getImgUrl() )?>'); "></div>
            </div>
        </div>
    </div>
</div>
