<?php

use backend\widgets\view\Collapse;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Currency
 * @var $categories common\models\Currency[]
 */
$model->created_at = Yii::$app->formatter->asDateTime($model->created_at);
$model->updated_at = Yii::$app->formatter->asDateTime($model->updated_at);

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';

    $model->status = 1;
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]); ?>

<div class="row">
    <div class="col-xs-6">
        <?php /* $form->field($model, 'status')->checkbox() */?>
        <?= $form->field($model, 'disable_sync')->checkbox() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'code_iso')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'value')->textInput() ?>
        <?= $form->field($model, 'fullName')->textInput() ?>
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
    <?php Collapse::begin([
        'title' => $model->getAttributeLabel('error'),
        'open' => $model->error
    ]) ?>
        <pre><?php print_r(json_decode($model->error, true)); ?></pre>
    <?php Collapse::end() ?>
<?php } ?>

<div class="form-group">
    <?= Html::submitButton(
        $model->isNewRecord ?
            t_b('Добавить') :
            t_b('Обновить'),
            [
               'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
               'disabled' => (bool) Yii::$app->keyStorage->get('backend.currency.sync.isStart')
            ]
    ) ?>
    <?= !$model->isNewRecord ? Html::a( t_b('Отмена'),
        ['index'],
        ['class' => 'btn btn-default']
    ) : '' ?>
</div>

<?php ActiveForm::end() ?>
