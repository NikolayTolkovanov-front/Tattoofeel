<?php
use yii\helpers\Html; ?>

<div class="home-slider">
    <div class="home-slider__list">
        <?php foreach($dataProvider->getModels() as $ban) { ?>
            <div class="home-slider__list__item">
                <a class="home-slider__list__img"
                   style="background-image: url('<?=$ban->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($ban->getImgUrl() )?>'); "
                        <?= $ban->title ? 'title="'.Html::encode($ban->title).'"' : '' ?>
                     <?= $ban->url ? 'href="'.$ban->url.'"' : '' ?>
                ></a>
                <a class="home-slider__list__img__mobile"
                   style="background-image: url('<?=$ban->getImg2Url()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($ban->getImg2Url() )?>'); "
                        <?= $ban->title ? 'title="'.Html::encode($ban->title).'"' : '' ?>
                     <?= $ban->url ? 'href="'.$ban->url.'"' : '' ?>
                ></a>
            </div>
        <?php } ?>
    </div>
</div>
