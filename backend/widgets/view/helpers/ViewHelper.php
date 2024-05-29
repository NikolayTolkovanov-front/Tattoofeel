<?php

namespace backend\widgets\view\helpers;

use common\models\User;
use trntv\yii\datetime\DateTimeWidget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use backend\widgets\view\grid\ActionColumn;
use backend\widgets\view\grid\BooleanColumn;

/**
 * Class ViewHelper
 * @package backend\helpers\ViewHelper
 */
class ViewHelper
{
    static function optionsDateWidgetDataColumn($searchModel, $attr_name, $min_width = '170px') {
        return [
            'attribute' => $attr_name,
            'options' => ['style' => "min-width: $min_width"],
            'format' => 'datetime',
            'filter' => DateTimeWidget::widget([
                'model' => $searchModel,
                'attribute' => $attr_name,
                'phpDatetimeFormat' => 'dd.MM.yyyy',
                'momentDatetimeFormat' => 'DD.MM.YYYY',
                'clientOptions' => [
                ],
                'clientEvents' => [
                    'dp.change' => new JsExpression('(e) => $(e.target).find("input").trigger("change.yiiGridView")')
                ],
            ]),
        ];
    }

    static function actionColumn($template = '<span style="white-space: nowrap;">{delete} {update}</span>') {
        return [
            'class' => ActionColumn::class,
            'options' => ['style' => 'white-space: nowrap;min-width:10px'],
            'template' => $template
        ];
    }

    static function booleanColumn($attribute, $label = null, $disabledColumn = false) {
        return [
            'class' => BooleanColumn::class,
            'attribute' => $attribute,
            'options' => [
                'style' => 'vertical-align: middle;text-align: center;min-width:10px'
            ],
            'format' => 'raw',
            'label' => $label,
            'value' => function($model, $key, $index, $column) use ($attribute, $disabledColumn) {
                if (!isset($column->grid->form))
                    return Html::tag('center',
                        Html::input('checkbox', null, null, [
                            'disabled' => true,
                            'checked' => $model->{$attribute} === 1,
                        ]));

                return $column->grid->form->field($model, "[$model->id]$attribute")->checkbox(null, null,
                    [
                        'disabled' => $disabledColumn,
                        'checked' => !!$model->{$attribute}
                    ])->label(false)->hint('');
            }
        ];
    }

    static function inputColumn($attribute, $options) {
        return [
            'attribute' => $attribute,
            'options' => $options,
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) use ($attribute) {
                return $column->grid->form->field($model, "[$model->id]$attribute")->label(false)->hint('');
            }
        ];
    }

    static function dropDownColumn($attribute, $filter, $options = ['style' => 'min-width: 200px']) {
        return [
            'attribute' => $attribute,
            'options' => $options,
            'filter' => $filter,
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) use ($attribute, $filter) {
                return $column->grid->form
                    ->field($model, "[$model->id]$attribute")
                    ->dropDownList($filter, ['prompt' => ''])
                    ->label(false)->hint('');
            }
        ];
    }

    static function getUsersMap() {
        $user[-1] = t_b('система');
        $user += ArrayHelper::map(User::find()->all(), 'id', 'username');
        return $user;
    }
}
