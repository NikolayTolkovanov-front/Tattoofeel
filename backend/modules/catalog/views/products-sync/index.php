<?php

use backend\widgets\view\helpers\ViewHelper;
use common\models\ProductCategoryConfig;
use common\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use backend\widgets\view\Collapse;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\catalog\models\search\ProductSyncSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['last_update_success'] =
    \Yii::$app->keyStorage->get('backend.products.sync.success_last_update');
$this->params['last_update_error'] =
    \Yii::$app->keyStorage->get('backend.products.sync.error_last_update');

$this->params['last_update_success'] =
    $this->params['last_update_success'] ?
        Yii::$app->formatter->asDateTime($this->params['last_update_success']) : '';

$this->params['last_update_error'] =
    $this->params['last_update_error'] ?
        Yii::$app->formatter->asDateTime($this->params['last_update_error']) : '';

?>

<?php
$this->title = t_b('Логи синхронизации');
$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="text-right" style="margin-bottom:10px">
    <?php
        $iconDelete = Html::tag('span','',['class' => 'glyphicon glyphicon-trash']).'&nbsp;&nbsp;';
        $iconSync = Html::tag('span','',['class' => 'glyphicon glyphicon-refresh']).'&nbsp;&nbsp;';
    ?>
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
    <?php //= Html::a($iconSync.t_b('Синхронизация'), ['sync'],['class' => 'btn btn-success']) ?>
    <?= Yii::$app->user->can('administrator') ?
        Html::a($iconDelete.t_b('Очистить логи'), ['clear'],['class' => 'btn btn-danger']) :
        null
    ?>
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
            ViewHelper::actionColumn(
                Yii::$app->user->can('administrator') ?
                    '{view} {delete}' :
                    '{view}'
            ),
            [
                'attribute' => 'id',
                'options' => ['style' => ['width'=>'80px','text-align'=>'right']],
            ],
            [
                'attribute' => 'status',
                'options' => [
                    'style' => 'vertical-align: middle;text-align: center;min-width:10px'
                ],
                'value' => function($model) {
                    return $model->statuses()[$model->status];
                }
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'date'),
            [
                'attribute' => 'author',
                'value' => function ($model) {
                    return $model->sender->username??null;
                },
                'filter' => ViewHelper::getUsersMap()
            ],
            [
                'attribute' => 'error',
                'label' => 'Логи/ошибки',
                'value' => function ($model) {
                    return $model->error ? substr($model->error, 0, 30).'...' : null;
                }
            ],
        ],
    ]); ?>

<?php Pjax::end() ?>
