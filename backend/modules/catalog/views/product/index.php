<?php

use common\models\ProductCategory;
use common\models\Brand;
use common\models\TypeEq;
use common\models\ProductCategoryConfig;
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
 * @var $searchModel  backend\modules\catalog\models\search\ProductSearch
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
            ViewHelper::booleanColumn('revised', t_b('Обр.')),
            ViewHelper::booleanColumn('disable_sync', t_b('Не Син.')),
            ViewHelper::booleanColumn('status', t_b('Пуб.')),
            ViewHelper::booleanColumn('is_ms_deleted', t_b('Уд. в МС')),
            [
                'attribute' => 'article',
                'options' => ['style' => 'min-width: 200px'],
            ],
            [
                'attribute' => 'slug',
                'options' => ['style' => 'min-width: 200px'],
            ],
            [
                'attribute' => 'title',
                'options' => ['style' => 'min-width: 200px'],
                'value' => function ($model) {
                    return Html::a($model->title, ['update', 'id' => $model->id],['data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            ViewHelper::dropDownColumn(
                'brand_id',
                ArrayHelper::map(Brand::find()->orderBy(['title'=>'ASC'])->all(), 'slug', 'title')
            ),
            ViewHelper::dropDownColumn(
                'type_eq',
                ArrayHelper::map(TypeEq::find()->orderBy(['title'=>'ASC'])->all(), 'id', 'title')
            ),
            ViewHelper::dropDownColumn(
                'category_ms_id',
                ArrayHelper::map(ProductCategory::find()->orderBy(['title'=>'ASC'])->all(), 'ms_id', 'title')
            ),
            ViewHelper::booleanColumn('is_main_in_config', t_b('Осн. конф.')),
            ViewHelper::dropDownColumn(
                'config_ms_id',
                ArrayHelper::map(ProductCategoryConfig::find()->orderBy(['title'=>'ASC'])->all(), 'ms_id', 'title'),
                ['style' => 'min-width: 170px']
            ),
            ViewHelper::inputColumn(
                'order',
                ['style' => 'min-width: 50px']
            ),
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'updated_at'),
        ];
        $columns_alt = [
            ['attribute' => 'ms_id'],
            ['attribute' => 'manufacturer'],
            ['attribute' => 'weight'],
            ['attribute' => 'amount'],
            ['attribute' => 'min_amount'],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'published_at'),
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
            [
                'attribute' => 'error',
                'options' => ['style' => 'min-width: 200px'],
                'value' => function ($model) {
                    return $model->error ? substr($model->error,0,40).'...' : null;
                },
                'format' => 'raw',
            ],
            ViewHelper::optionsDateWidgetDataColumn($searchModel, 'created_at'),
            [
                'attribute' => 'productFilters',
                'value' => function ($model) {
                    $r = [];
                    foreach($model->productFilters as $f)
                        $r[] = $f->category->title.' -> '.$f->title;

                    return implode('<br />',$r);
                },
                'format' => 'raw',
            ],
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
                    Html::a(t_b('Все'),
                        Url::current(['index', 'ProductSearch' =>
                            ['revised'=>null,'updated_by'=>null], 'sort'=>null]),
                        ['class' => ['btn btn-default',
                            (
                                !isset($qs['ProductSearch']['revised']) ||
                                $qs['ProductSearch']['revised'] === ''
                            ) && !(
                                isset($qs['ProductSearch']['updated_by']) &&
                                $qs['ProductSearch']['updated_by'] === '-1' &&
                                isset($qs['sort']) &&
                                $qs['sort'] === '-updated_at'
                            )
                                ? 'active' : null]]
                    ),
                    Html::a(t_b('Буфер'),
                        Url::current(['index', 'ProductSearch' =>
                            ['revised'=>0,'updated_by'=>null], 'sort'=>null]),
                        ['class' => ['btn btn-default',
                            isset($qs['ProductSearch']['revised']) &&
                            $qs['ProductSearch']['revised'] === '0'
                                ? 'active' : null]]
                    ),
                    Html::a(t_b('Обработанные'),
                        Url::current(['index', 'ProductSearch' =>
                            ['revised'=>1,'updated_by'=>null], 'sort'=>null]),
                        ['class' => ['btn btn-default',
                            isset($qs['ProductSearch']['revised']) &&
                            $qs['ProductSearch']['revised'] === '1'
                                ? 'active' : null]]
                    ),
                    Html::a(t_b('Обновленные'),
                        Url::current(['index', 'ProductSearch' =>
                            ['revised'=>null,'updated_by'=>-1], 'sort' => '-updated_at']),
                        ['class' => ['btn btn-default',
                            isset($qs['ProductSearch']['updated_by']) &&
                            $qs['ProductSearch']['updated_by'] === '-1' &&
                            isset($qs['sort']) &&
                            $qs['sort'] === '-updated_at'
                                ? 'active' : null]]
                    ),
                    Html::a('Загрузить изобр.',Url::to(['/catalog/product/save-images']),
                        [
                            'class' => ['btn btn-default'],
                            'data-pjax' => false
                        ]
                    ),
                    Html::a(t_b('Добавить продукт'), ['create'], ['class' => 'btn btn-success']),
                    Html::submitButton(t_b('Сохранить'),['class' => 'btn btn-primary']),
                ]
            ]);
        ActiveForm::end();

    ?>

<?php Pjax::end() ?>
