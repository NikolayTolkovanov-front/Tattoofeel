<?php

use backend\widgets\view\helpers\ViewHelper;
use common\grid\EnumColumn;
use common\models\ArticleCategory;
use yii\grid\GridView;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\system\models\search\KeyStorageItemSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        common\models\KeyStorageItem
 */

$this->title = Yii::t('backend', 'Ключ/значение');

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-xs-6">
        <div class="box box-success collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo Yii::t('backend', 'Добавить Ключ/значение') ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => [
        'class' => 'grid-view table-responsive',
    ],
    'columns' => [
        ViewHelper::actionColumn(),
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'key',
            'options' => ['style' => 'width: 200px'],
            'headerOptions' => ['style' => 'width: 200px'],
        ],
        [
            'attribute' => 'value',
            'options' => ['style' => 'width: 40%'],
            'headerOptions' => ['style' => 'width: 40%'],
        ],
        'comment'
    ],
]); ?>
