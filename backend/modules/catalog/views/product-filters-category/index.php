<?php

use backend\widgets\view\helpers\ViewHelper;
use common\models\ProductFilters;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\widgets\view\Collapse;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\catalog\models\search\ProductFiltersSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        ProductFilters
 * @var $categories   common\models\ProductFilters[]
 */

?>

<?php
$this->title = t_b('Категории фильтров');
$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-8">
        <?php Collapse::begin([
            'title' => 'Добавить каиегорию',
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
            ViewHelper::booleanColumn('status', t_b('Акт.')),
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
        ],
    ]); ?>

<?php Pjax::end() ?>
