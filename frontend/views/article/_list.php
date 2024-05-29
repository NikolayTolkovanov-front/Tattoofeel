<?php

/**
 * @var $dataProvider common\models\articleN
 * @var $isAjax bool
 */

use frontend\widgets\common\articles\alist\CommonArticlesList;
use yii\helpers\Url;

?>

<?= CommonArticlesList::widget([
    'dataProvider' => $dataProvider,
    'isAjax' => isset($isAjax) ? $isAjax : false,
    //'linkLoadMore' => Url::to(['/articleN'])
    'linkLoadMore' => Url::to(['/article'])
]) ?>

