<?php

use backend\widgets\view\Collapse;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\UserClient;

/* @var $this yii\web\View */
/* @var $model backend\modules\client\models\UserClientForm */
/* @var $form yii\bootstrap\ActiveForm */

$model->getModel()->created_at = $model->getModel()->created_at ?
    Yii::$app->formatter->asDateTime($model->getModel()->created_at) : '';
$model->getModel()->updated_at = $model->getModel()->updated_at ?
    Yii::$app->formatter->asDateTime($model->getModel()->updated_at) : '';

$model->getModel()->created_by = $model->getModel()->author->username??'';
$model->getModel()->updated_by = $model->getModel()->updater->username??'';

$model->getModel()->client_created_at = $model->getModel()->client_created_at ?
    Yii::$app->formatter->asDateTime($model->getModel()->client_created_at) : '';
$model->getModel()->client_updated_at = $model->getModel()->client_updated_at ?
    Yii::$app->formatter->asDateTime($model->getModel()->client_updated_at) : '';

$model->getModel()->client_created_by = $model->getModel()->authorClient->username??'';
$model->getModel()->client_updated_by = $model->getModel()->updaterClient->username??'';

if ($model->getModel()->isNewRecord) {
    $model->status = 2;
}

?>

<div class="user-form">

    <?php $form = ActiveForm::begin() ?>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'username') ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'email') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'password')->passwordInput() ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'status')->dropDownList(UserClient::statuses()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'is_manager')->checkbox() ?>
            </div>
        </div>


    <?php if (!$model->getModel()->isNewRecord) { ?>
        <?php Collapse::begin([
            'title' => 'Создал/обновил',
            'open' => false
        ]) ?>        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'client_created_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'client_created_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'client_updated_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'client_updated_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'created_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'created_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'updated_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model->getModel(), 'updated_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <?php Collapse::end() ?>

    <?php } ?>

        <div class="form-group">
            <?= Html::submitButton(
                $model->getModel()->isNewRecord ?
                    t_b('Добавить') :
                    t_b('Обновить'),
                [
                    'class' => $model->getModel()->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                ]
            ) ?>
            <?= !$model->getModel()->isNewRecord ? Html::a( t_b('Отмена'),
                ['index'],
                ['class' => 'btn btn-default']
            ) : '' ?>
        </div>
    <?php ActiveForm::end() ?>

</div>
