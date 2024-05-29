<?php
/**
* @var $dataProvider
* @var $searchModel
 */

use backend\widgets\view\helpers\ViewHelper;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = t_b('Разрешенные IP адреса');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">

    <p class="text-right">
        <?php echo Html::a(t_b( 'Добавить IP адрес'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'ip_address',
            ],
            [
                'attribute' => 'comment',
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'updated_at'),
        ],
    ]); ?>

</div>
