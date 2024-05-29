<div class="product-card-pict">
    <div class="product-card-pict__list-main-img" id="pr-pict-main">
        <?php foreach($model->bigImages_ as $p) { ?>
            <span class="product-card-pict__list-main-img__item"
                  style="background-image: url('<?=$p->getBigImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($p->getBigImgUrl() )?>'); "></span>
        <?php } ?>
        <?php if(empty($model->bigImages_)) { ?>
            <span class="product-card-pict__list-main-img__item"
                  style="background-image:url(<?= Yii::$app->params['default_pict'] ?>)"></span>
        <?php } ?>
    </div>
    <div class="product-card-pict__list-img<?= count($model->bigImages_) > 1 ? '' : ' none' ?>" id="pr-pict">
        <?php foreach($model->bigImages_ as $p) { ?>
            <span class="product-card-pict__list-img__item"><span
                        style="background-image:url(<?= $p->getBigImgUrl() ?>)"></span></span>
        <?php } ?>
    </div>
</div>

<?php if (count($model->bigImages)):?>
    <a style="display: none;" href="<?=$model->bigImages[0]->getBigImgUrl()?>">
        <img src="<?=$model->bigImages[0]->getBigImgUrl()?>" title="<?=$model->title?>" itemprop="image">
    </a>
<?php endif;?>