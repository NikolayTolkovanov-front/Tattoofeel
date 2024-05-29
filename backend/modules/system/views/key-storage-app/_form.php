<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this  yii\web\View
 * @var $model common\models\KeyStorageItem
 * @var $form  yii\bootstrap\ActiveForm
 */

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]) ?>

<?php echo $form->field($model, 'key')->textInput() ?>

<?php echo $form->field($model, 'value')->textarea() ?>

<?php echo $form->field($model, 'comment')->textarea() ?>

<div class="form-group">
    <?php echo !$model->isNewRecord ? Html::submitButton( Yii::t('backend', 'Обновить'),
        ['class' => 'btn btn-primary', 'name' => 'update', 'value' => 1]) : null ?>
    <?php echo Html::submitButton( Yii::t('backend', 'Сохранить'),
        ['class' => 'btn btn-success', 'name' => 'save', 'value' => 1]) ?>
    <?php echo Html::a( Yii::t('backend', 'Отменить'), Url::to(['index']),
        ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end() ?>
