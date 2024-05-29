<?php

/**
 * @var $brandDataProvider common\models\Brand
 * @var $isAjax bool
 */

use yii\helpers\Url;
use frontend\widgets\categories\tile\CategoriesTile;

?>

<?= CategoriesTile::widget([
    'isAjax' => isset($isAjax) ? $isAjax : false,
    'linkLoadMore' => Url::to(['/brands']),
    'outerTitle' => true,
    'simpleList' => true,
    'dataProvider' => $brandDataProvider
]) ?>

