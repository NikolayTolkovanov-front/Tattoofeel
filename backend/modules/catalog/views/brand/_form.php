<?php

use backend\widgets\form\Imperavi;
use backend\widgets\form\upload\Upload;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Brand
 * @var $categories common\models\Brand[]
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
        <?= $form->field($model, 'isMain')->checkbox() ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
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

<br />
<div class="row">
    <div class="col-xs-8">
        <div id="seo-snippet" class="seo-snippet">
            <a href="<?=Yii::$app->request->hostInfo."/brands/{$model->slug}/"?>" target="_blank" class="seo-snippet-link">
                <h3 id="seo-snippet-title" class="seo-snippet-title"><?=$model->seo_title ?: 'Seo Заголовок'?></h3>
                <span id="seo-snippet-url" class="seo-snippet-url"><?=Yii::$app->request->hostInfo."/brands/{$model->slug}/"?></span>
            </a>
            <p id="seo-snippet-desc" class="seo-snippet-desc"><?=$model->seo_desc ?: 'Seo Описание'?></p>
        </div>
    </div>
</div>

<br />
<br />

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'seo_title')->textInput([
            'id' => 'seo-title-field',
        ]) ?>
        <?= $form->field($model, 'seo_keywords')->textInput([
            'id' => 'seo-keywords-field',
        ]) ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'seo_desc')->textarea([
            'style'=>'height: 108px',
            'id' => 'seo-desc-field',
        ]) ?>
    </div>
</div>
<br />

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
