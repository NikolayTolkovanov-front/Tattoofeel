<?php
/**
 * @var $model mixed
 */
use yii\helpers\Url;

$this->params['section'] = Yii::$app->controller->id == 'site' ? 'catalog' : Yii::$app->controller->id;

?>
<a href="<?= Url::to(['/'.$this->params['section'].'/'.$model->slug]) ?>">
    <div><div style="background-image: url('<?=$model->getImgUrl()?>'); background-image: url('<?=  \common\components\Common::ImageReplace($model->getImgUrl() )?>'); "></div></div>

    <div class="category-tile-caption-container">
        <span class="category-tile-caption"><?= $model->title ?></span>
    </div>
</a>
