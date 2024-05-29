<?php

use yii\helpers\ArrayHelper;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@frontend/views/layouts/_clear.php');

use yii\widgets\Pjax; ?>
<div class="hidden">
        <?= $this->render('_header'); ?>
        <?= $this->render('_menu'); ?>

        <?php Pjax::begin(['id' => 'pjax-main']); ?>
            <?= $this->render('_breadcrumbs'); ?>
            <?= $content ?>
        <?php Pjax::end() ?>

        <?= $this->render('_footer'); ?>
    <div class="pop-mes">
        <?php if(Yii::$app->session->hasFlash('alert')):?>
            <span data-hide-timeout class="pop-mes__item"><?= ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'); ?></span>
        <?php endif; ?>
    </div>
</div>
<?php $this->endContent() ?>
