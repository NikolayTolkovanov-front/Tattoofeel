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

$this->title = t_b('Клиенты');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p class="text-right">
        <?php echo Html::a(t_b( 'Добавить клиента'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
        'options' => [
            'class' => 'grid-view table-responsive',
        ],
        'columns' => [
            ViewHelper::actionColumn('<span style="white-space: nowrap;">{delete} {update} {profile}</span>'),
            [
                'attribute' => 'id',
                'options' => ['style' => ['width'=>'60px','text-align'=>'right']],
                'contentOptions' => ['style' => ['width'=>'60px','text-align'=>'right']],
                'headerOptions' => ['style' => ['width'=>'60px','text-align'=>'right']],
            ],
            [
                'class' => EnumColumn::class,
                'attribute' => 'status',
                'enum' => UserClient::statuses(),
                'filter' => UserClient::statuses()
            ],
            [
                'attribute' => 'username',
                'options' => ['style' => 'min-width: 150px'],
                'value' => function ($model) {
                    return Html::a($model->username, ['update', 'id' => $model->id]);
                },
                'format' => 'raw',
            ],
            'email',
            ViewHelper::booleanColumn('is_manager'),
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'logged_at'),
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'client_created_at'),
        ],
    ]); ?>

</div>
