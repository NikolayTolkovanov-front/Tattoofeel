<?php

use backend\widgets\form\DateTime;
use yii\helpers\ArrayHelper;
//use common\models\Brand;

/**
 * @var $categories common\models\ProductCategory[]
 * @var $categoriesConfig
 */

//echo '<pre>';print_r($model);echo '</pre>';
?>
<br />
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'id')->textInput(['disabled'=>true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'coupon_code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'active_until')->widget(DateTime::class) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'uses_count')->textInput() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'used_count')->textInput(['disabled'=>true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'is_one_user')->checkbox() ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'is_one_product')->checkbox() ?>
    </div>
</div>

<br />
<br />
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'is_percent')->checkbox() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'coupon_value')->textInput() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'order_sum_min')->textInput() ?>
    </div>
</div>
<br />
