<?php
/**
* @var $dataProvider
* @var $searchModel
 */

use backend\widgets\view\helpers\ViewHelper;
use common\grid\EnumColumn;
use common\models\UserClient;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

$this->title = t_b('Отложенные товары');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">

    <p class="text-right">
        <?php echo Html::a(t_b( 'Добавить отложенный товар'), ['create'], ['class' => 'btn btn-success']) ?>
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
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model->user->username??null;
                },
                'label' => 'Клиент'
            ],
            [
                'attribute' => 'product_id',
                'value' => function ($model) {
                    return $model->product->title??null;
                },
                'label' => 'Продукт'
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'client_created_at'),
        ],
    ]); ?>

</div>
