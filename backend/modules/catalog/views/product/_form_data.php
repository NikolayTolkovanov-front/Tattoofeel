<?php

use backend\widgets\form\DateTime;
use yii\helpers\ArrayHelper;
use common\models\Currency;
use common\models\Brand;
use common\models\TypeEq;

/**
 * @var $categories common\models\ProductCategory[]
 * @var $categoriesConfig
 */
?>
<br />
<div class="row">
    <div class="col-xs-2">
        <?= $form->field($model, 'status')->checkbox() ?>
    </div>
    <div class="col-xs-2">
        <?= $form->field($model, 'is_ms_deleted')->checkbox() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'published_at')->widget(DateTime::class) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'id')->textInput(['disabled'=>true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'title_short')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'article')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?php /* $form->field($model, 'order')->textInput() */ ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'order_config')->textInput() ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'category_ms_id')->dropDownList(
            ArrayHelper::map($categories, 'ms_id','title'), ['prompt' => '']) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'config_ms_id')->dropDownList(
            ArrayHelper::map($categoriesConfig,'ms_id','title'), ['prompt' => '']) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'is_main_in_config')->checkbox() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-3">
        <?= $form->field($model, 'weight')->textInput() ?>
    </div>
    <div class="col-xs-3">
        <?= $form->field($model, 'length')->textInput() ?>
    </div>
    <div class="col-xs-3">
        <?= $form->field($model, 'width')->textInput() ?>
    </div>
    <div class="col-xs-3">
        <?= $form->field($model, 'height')->textInput() ?>
    </div>
    <div class="col-xs-12">
        <?= $form->field($model, 'is_oversized')->checkbox() ?>
    </div>
    <div class="col-xs-12">
        <?= $form->field($model, 'is_discount')->checkbox() ?>
    </div>
    <div class="col-xs-12">
        <?= $form->field($model, 'is_super_price')->checkbox() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'amount')->textInput() ?>
        <?php /* $form->field($model, 'min_amount')->textInput() */ ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'manufacturer')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'display_currency')->dropDownList(
            ArrayHelper::map(Currency::find()->where(['status' => 1])->all(),'id','fullName'), ['prompt'=>'']) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'brand_id')->dropDownList(
            ArrayHelper::map(Brand::find()->all(),'slug','title'), ['prompt'=>'']) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'type_eq')->dropDownList(
            ArrayHelper::map(TypeEq::find()->all(),'id','title'), ['prompt'=>'']) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'is_new')->checkbox() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'is_new_at')->widget(DateTime::class) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'view_count')->textInput() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'config_name')->textInput() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'config_decrypt')->textarea() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'alt_desc')->textarea() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'warranty')->textInput() ?>
    </div>
</div>
<br />
