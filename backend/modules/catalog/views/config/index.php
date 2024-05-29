<?php

use backend\widgets\view\helpers\ViewHelper;
use common\models\ProductCategoryConfig;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\widgets\view\Collapse;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\catalog\models\search\ProductCategoryConfigSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        productCategoryConfig
 * @var $categoryConfig_s   common\models\productCategoryConfig[]
 */

?>

<?php
$this->title = t_b('Конфигурации продуктов');
$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = $this->title;


$this->params['last_update_success'] =
    \Yii::$app->keyStorage->get('backend.productConfig.sync.success_last_update');
$this->params['last_update_error'] =
    \Yii::$app->keyStorage->get('backend.productConfig.sync.error_last_update');

$this->params['last_update_success'] =
    $this->params['last_update_success'] ?
        Yii::$app->formatter->asDateTime($this->params['last_update_success']) : '';

$this->params['last_update_error'] =
    $this->params['last_update_error'] ?
        Yii::$app->formatter->asDateTime($this->params['last_update_error']) : '';


?>

<div class="row">
    <div class="col-sm-8">
        <?php Collapse::begin([
            'title' => 'Добавить конфигурацию',
            'open' => $model && $model->hasErrors()
        ]) ?>
        <?php echo $this->render('_form', [
            'model' => $model
        ]) ?>
        <?php Collapse::end() ?>
    </div>
    <div class="col-sm-4 text-right">
        <?php
        echo $this->params['last_update_success'] ?
            Html::tag('span',
                'Последняя синх.: '.$this->params['last_update_success'],
                ['class'=>'text-success']
            ) : Html::tag('span',
                'Последняя синх.: '.$this->params['last_update_error']?:t_b('не известно'),
                ['class'=>'text-danger']
            );
        ?><?= ' &nbsp; ' ?>
        <?= Html::a(
            "<i class='glyphicon glyphicon-refresh'></i>&nbsp; ".t_b('синхронизация'),
            ['sync'],
            ['class' => ['btn','btn-success']]
        ) ?>
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
            ViewHelper::booleanColumn('disable_sync', t_b('Не Син.')),
            ViewHelper::booleanColumn('status'),
            [
                'attribute' => 'ms_id',
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
                'attribute' => 'error',
                'options' => ['style' => 'min-width: 200px'],
                'value' => function ($model) {
                    return $model->error ? substr($model->error,0,40).'...' : null;
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
