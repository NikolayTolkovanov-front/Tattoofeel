<?php

use backend\widgets\view\Collapse;
use common\models\Subdomains;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\UserClient;

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
                <?= $form->field($model, 'subdomain')->textInput()?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'city')->textInput()?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'word_form')->textInput()?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <?php echo $form->field($model, 'address')->textarea()?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-4">
                <?php echo $form->field($model, 'phone')->textInput()?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'work_time')->textInput()?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'work_hours_showroom')->textInput()?>
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
            <?= Html::submitButton(
                $model->isNewRecord ?
                    t_b('Добавить') :
                    t_b('Обновить'),
                [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                ]
            ) ?>
            <?= !$model->isNewRecord ? Html::a( t_b('Отмена'),
                ['index'],
                ['class' => 'btn btn-default']
            ) : '' ?>
        </div>
    <?php ActiveForm::end() ?>

</div>
