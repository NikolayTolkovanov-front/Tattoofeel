<?php

/**
 * @var $dataProvider common\models\News
 * @var $isAjax bool
 */

use frontend\widgets\common\articles\alist\CommonArticlesList;
use yii\helpers\Url;

?>

<?= CommonArticlesList::widget([
    'dataProvider' => $dataProvider,
    'isAjax' => isset($isAjax) ? $isAjax : false,
    'linkLoadMore' => Url::to(['/news'])
]) ?>

