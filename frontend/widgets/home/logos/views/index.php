<?php
/**
* @var $dataProvider
 */
use \yii\helpers\Url;
?>
<div class="logos-block">
    <div class="logos-block__head">
        <h2 class="h1 center">
            <a href="<?= Url::to(['/brands']) ?>">Мы сотрудничаем</a>
        </h2>
    </div>
    <div class="logos-block__list">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php foreach($dataProvider->getModels() as $item) { ?>
                    <a href="<?= Url::to(['/brands/'.$item->slug]) ?>"
                       class="logos__item swiper-slide">
                        <div class="logos__item__img" style="background-image: url('<?=$item->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($item->getImgUrl() )?>'); "></div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="logos-block__more">
        <a href="<?= Url::to(['/brands']) ?>" class="btn-bord">Показать все фирмы</a>
    </div>
</div>
