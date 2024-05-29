<?php
/**
* @var $dataProvider
* @var $searchModel
 */

use backend\widgets\view\helpers\ViewHelper;
use common\grid\EnumColumn;
use common\models\EmailTemplate;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

Yii::$app->cache->flush();

$this->title = t_b('Отзывы');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">

    <p class="text-right">
        <?php echo Html::a(t_b( 'Добавить отзыв'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
            ViewHelper::booleanColumn('is_published'),
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'date'),
            [
                'attribute' => 'user_client_id',
                'value' => function ($model) {
                    return $model->userClient->profile->full_name??null;
                },
            ],
            [
                'attribute' => 'product_id',
                'value' => function ($model) {
                    return $model->product->title??null;
                },
            ],
            [
                'attribute' => 'rating',
            ],
            [
                'attribute' => 'text',
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'updated_at'),
        ],
    ]); ?>

</div>
