<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

frontend\assets\MaintenanceAsset::register($this);

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <?php echo Html::csrfMetaTags() ?>
        <?php

        foreach(array_merge(
                    [
                        'description' => ['name' => 'description', 'content' => Yii::$app->params['description']],
                        'keywords' => ['name' => 'keywords', 'content' => Yii::$app->params['keywords']]
                    ], \Yii::$app->params['meta']
                ) as $meta)
            echo $this->registerMetaTag($meta);
        ?>

    </head>
    <body id="body">
    <div class="hidden">
        <?php $this->beginBody() ?>
        <?= $this->render('_header-light'); ?>

        <div class="main">
            <?php echo $content ?>
        </div>

        <?= $this->render('_footer-light'); ?>
        <?php $this->endBody() ?>
    </div>
    </body>
    </html>
<?php $this->endPage() ?>
