<?php

use backend\widgets\view\helpers\ViewHelper;
use common\models\ProductPriceTemplate;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\widgets\view\Collapse;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\catalog\models\search\ProductPriceTemplateSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        productPriceTemplate
 * @var $categories   common\models\productPriceTemplate[]
 */

?>

<?php
$this->title = t_b('Цены (скидки)');
$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!--
<div class="row">
    <div class="col-sm-8">
        <?php Collapse::begin([
            'title' => 'Создать Скидку',
            'open' => $model && $model->hasErrors()
        ]) ?>
        <?php echo $this->render('_form', [
            'model' => $model
        ]) ?>
        <?php Collapse::end() ?>
    </div>
    <div class="col-sm-4">

    </div>
</div>
-->

<?php Pjax::begin(); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
        'options' => [
            'class' => 'grid-view table-responsive',
        ],
        'columns' => [
            //ViewHelper::actionColumn(),
            [
                'attribute' => 'id',
                'options' => ['style' => ['min-width'=>'80px','text-align'=>'right']],
            ],
            [
                'attribute' => 'title',
                'options' => ['style' => 'min-width: 200px'],
                /*
                'value' => function ($model) {
                    return Html::a($model->title, ['update', 'id' => $model->id]);
                },
                'format' => 'raw',
                */
            ]
        ],
    ]); ?>

<?php Pjax::end() ?>
