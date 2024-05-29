<?php

use backend\widgets\view\helpers\ViewHelper;
use common\models\News;
use trntv\yii\datetime\DateTimeWidget;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\widgets\view\Collapse;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\content\models\search\NewsSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        News
 * @var $categories   common\models\News[]
 */

?>

<?php
$this->title = t_b('Новости');
$this->params['breadcrumbs'][] = ['label' => t_b('Контент'), 'url' => ['/news/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-8">
        <?php Collapse::begin([
            'title' => 'Добавить новость',
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
                'attribute' => 'title',
                'options' => ['style' => 'min-width: 150px'],
                'value' => function ($model) {
                    return Html::a($model->title, ['update', 'id' => $model->id]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'slug',
                'options' => ['style' => 'min-width: 150px'],
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model->updater->username??null;
                },
                'filter' => ViewHelper::getUsersMap()
            ],
            [
                'attribute' => 'published_at',
                'options' => ['style' => 'width: 10%'],
                'format' => 'datetime',
                'filter' => DateTimeWidget::widget([
                    'model' => $searchModel,
                    'attribute' => 'published_at',
                    'phpDatetimeFormat' => 'dd.MM.yyyy',
                    'momentDatetimeFormat' => 'DD.MM.YYYY',
                    'clientEvents' => [
                        'dp.change' => new JsExpression('(e) => $(e.target).find("input").trigger("change.yiiGridView")'),
                    ],
                ]),
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'updated_at'),
        ],
    ]); ?>

<?php Pjax::end() ?>
