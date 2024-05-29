<?php
/**
* @var $dataProvider
* @var $searchModel
 */

use backend\widgets\view\helpers\ViewHelper;
use common\grid\EnumColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\UserClientOrder;

$this->title = t_b('Заказы');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">

    <p class="text-right">
        <?php echo Html::a(t_b( 'Добавить заказ'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'order_ms_number',
            ],
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model->user->username??null;
                },
            ],
            ViewHelper::booleanColumn('isCart'),
            [
                'class' => EnumColumn::class,
                'attribute' => 'status_pay',
                'enum' => UserClientOrder::statusesPay(),
                'filter' => UserClientOrder::statusesPay()
            ],
            [
                'class' => EnumColumn::class,
                'attribute' => 'status_delivery',
                'enum' => UserClientOrder::statusesDelivery(),
                'filter' => UserClientOrder::statusesDelivery()
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'date'),
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'client_updated_at'),
        ],
    ]); ?>

</div>
