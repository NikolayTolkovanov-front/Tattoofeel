<?php

use backend\widgets\form\Imperavi;
use backend\widgets\form\upload\Upload;
use trntv\yii\datetime\DateTimeWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Stock
 * @var $categories common\models\Stock[]
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
        <?= $form->field($model, 'widget_id')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'body_short')->widget(Imperavi::class, ['height'=>200]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'body')->widget(Imperavi::class, ['height'=>200]) ?>

    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'thumbnail')->widget(Upload::class) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'custom_1')->textInput() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'custom_2')->textInput() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'custom_3')->textInput() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'custom_4')->textInput() ?>
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
