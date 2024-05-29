<?php

use backend\widgets\form\Imperavi;
use backend\widgets\form\upload\Upload;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\ProductFiltersCategory
 * @var $categories common\models\ProductFiltersCategory[]
 */
$model->created_at = Yii::$app->formatter->asDateTime($model->created_at);
$model->updated_at = Yii::$app->formatter->asDateTime($model->updated_at);

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';

if ($model->isNewRecord)
    $model->status = 1;

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]); ?>

<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'status')->checkbox() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<?php if (!$model->isNewRecord) { ?>
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
