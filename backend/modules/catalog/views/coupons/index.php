<?php

use common\models\ProductCategory;
use common\models\Brand;
use backend\widgets\view\grid\SmartGridView;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use backend\widgets\view\helpers\ViewHelper;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use \yii\helpers\Url;

/**
 * @var $this         yii\web\View
 * @var $searchModel  backend\modules\catalog\models\search\CouponsSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $queryParam__perPage
 * @var $queryParam__sh
 * @var $qs
 */
?>

<?php
$this->title = t_b('Каталог');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin(['clientOptions' => ['method' => 'POST']]); ?>
    <?php if (Yii::$app->session->hasFlash('alertError')): ?>
        <?php echo Alert::widget([
            'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alertError'), 'body'),
            'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alertError'), 'options'),
        ]) ?>
    <?php endif; ?>
    <?php

        $columns_main =  [
            [
                'attribute' => 'id',
                'options' => ['style' => ['min-width'=>'80px','text-align'=>'right']],
            ],
            [
                'attribute' => 'coupon_code',
                'options' => ['style' => 'min-width: 200px'],
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'active_until'),
            ['attribute' => 'uses_count'],
            ['attribute' => 'used_count'],
            ViewHelper::booleanColumn('is_percent'),
            ViewHelper::booleanColumn('is_one_user'),
            ViewHelper::booleanColumn('is_one_product'),
            ['attribute' => 'coupon_value'],
            ['attribute' => 'order_sum_min'],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'updated_at'),
        ];
        $columns_alt = [
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->author->username??null;
                },
                'filter' => ViewHelper::getUsersMap()
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model->updater->username??null;
                },
                'filter' => ViewHelper::getUsersMap()
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'created_at'),
        ];
        $columns_action = [ViewHelper::actionColumn()];

        $form = ActiveForm::begin(['options' => ['data-pjax' => true]]);
            echo SmartGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns_main' => $columns_main,
                'columns_alt' => $columns_alt,
                'columns_actions' => $columns_action,
                'show_columns' => $queryParam__sh,
                'pageSize' => $queryParam__perPage,
                'scrollLeft' => $qs['scrollLeft']??0,
                'layout' => "{topButtons}\n{summary}\n{pager}\n{items}\n{summary}\n{pager}",
                'form' => $form,
                'topButtons' => [
                    Html::a('<','#left-grid-view_smart__table-container',
                        ['class' => ['btn btn-default pull-left'],
                            'data-pjax' => false,
                            'style'=>'margin-right:10px']
                    ),
                    Html::a('>','#right-grid-view_smart__table-container',
                        ['class' => ['btn btn-default pull-left'],
                            'data-pjax' => false]
                    ),
//                    Html::a(t_b('Все'),
//                        Url::current(['index', 'CouponsSearch' =>
//                            ['updated_by' => null], 'sort' => null]),
//                        ['class' => ['btn btn-default',
//                            !(
//                                isset($qs['CouponsSearch']['updated_by']) &&
//                                $qs['CouponsSearch']['updated_by'] === '-1' &&
//                                isset($qs['sort']) &&
//                                $qs['sort'] === '-updated_at'
//                            )
//                                ? 'active' : null]]
//                    ),
                    Html::a(t_b('Добавить купон'), ['create'], ['class' => 'btn btn-success']),
                    //Html::submitButton(t_b('Сохранить'),['class' => 'btn btn-primary']),
                ]
            ]);
        ActiveForm::end();
    ?>

<?php Pjax::end() ?>
