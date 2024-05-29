<?php

use backend\widgets\form\DateTime;
use backend\widgets\form\Imperavi;
use backend\widgets\view\Collapse;
use common\models\EmailTemplate;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model */
/* @var $form yii\bootstrap\ActiveForm */

$model->created_at = $model->created_at ?
    Yii::$app->formatter->asDateTime($model->created_at) : '';
$model->updated_at = $model->updated_at ?
    Yii::$app->formatter->asDateTime($model->updated_at) : '';

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';

?>

<div class="user-form">

    <?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-xs-4">
            <?= $form->field($model, 'is_published')->checkbox() ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'id')->textInput(['disabled'=>true]) ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'user_client_id')->textInput(['disabled' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4">
            <?= $form->field($model, 'product_id')->textInput(['disabled' => true]) ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'rating')->textInput()?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'date')->widget(DateTime::class) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?php echo $form->field($model, 'text')->textarea(['style'=>'height: 200px']) ?>
        </div>
    </div>

    <?php if (!$model->isNewRecord) { ?>
        <?php Collapse::begin([
            'title' => 'Создал/обновил',
            'open' => false
        ]) ?>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'created_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'created_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'updated_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'updated_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <?php Collapse::end() ?>

    <?php } ?>

    <div class="form-group">
        <?php echo !$model->isNewRecord ? Html::submitButton( Yii::t('backend', 'Обновить'),
            ['class' => 'btn btn-primary', 'name' => 'update', 'value' => 1]) : null ?>
        <?php echo Html::submitButton( Yii::t('backend', 'Сохранить'),
            ['class' => 'btn btn-success', 'name' => 'save', 'value' => 1]) ?>
        <?php echo Html::a( Yii::t('backend', 'Отменить'), Url::to(['index']),
            ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end() ?>

</div>
