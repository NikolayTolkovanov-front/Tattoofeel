<?php

use backend\widgets\view\helpers\ViewHelper;
use common\models\BlockWidget;
use trntv\yii\datetime\DateTimeWidget;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\widgets\view\Collapse;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\content\models\search\BlockWidgetSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        BlockWidget
 * @var $categories   common\models\BlockWidget[]
 */

?>

<?php
$this->title = t_b('Виджеты');
$this->params['breadcrumbs'][] = ['label' => t_b('Контент'), 'url' => ['/block-widget/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-8">
        <?php Collapse::begin([
            'title' => 'Добавить виджет',
            'open' => $model && $model->hasErrors()
        ]) ?>
        <?php echo $this->render('_form', [
            'model' => $model
        ]) ?>
        <?php Collapse::end() ?>
    </div>
    <div class="col-sm-4 text-right">
    </div>
</div>

<?php Pjax::begin(); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
        'options' => [
            'class' => 'grid-view table-responsive',
        ],
        'columns' => [
            ViewHelper::actionColumn(),
            [
                'attribute' => 'id',
                'options' => ['style' => ['width'=>'60px','text-align'=>'right']],
                'contentOptions' => ['style' => ['width'=>'60px','text-align'=>'right']],
                'headerOptions' => ['style' => ['width'=>'60px','text-align'=>'right']],
            ],
            ViewHelper::booleanColumn('status'),
            [
                'attribute' => 'widget_id',
                'options' => ['style' => 'min-width: 150px'],
            ],
            [
                'attribute' => 'title',
                'options' => ['style' => 'min-width: 150px'],
                'value' => function ($model) {
                    return Html::a($model->title, ['update', 'id' => $model->id]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model->updater->username??null;
                },
                'filter' => ViewHelper::getUsersMap()
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'updated_at'),
        ],
    ]); ?>

<?php Pjax::end() ?>
