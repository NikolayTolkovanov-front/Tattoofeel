<?php

use backend\widgets\form\Imperavi;
use backend\widgets\view\Collapse;
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
        <div class="col-xs-6">
            <?= $form->field($model, 'url')->textInput([
                'id' => 'url-field',
            ]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'h1')->textInput([
                'id' => 'h1-field',
            ]) ?>
        </div>
    </div>

    <br />
    <div class="row">
        <div class="col-xs-8">
            <div id="seo-snippet" class="seo-snippet">
                <a href="<?=Yii::$app->request->hostInfo."{$model->url}"?>" target="_blank" class="seo-snippet-link">
                    <h3 id="seo-snippet-title" class="seo-snippet-title"><?=$model->seo_title ?: 'Seo Заголовок'?></h3>
                    <span id="seo-snippet-url" class="seo-snippet-url"><?=Yii::$app->request->hostInfo."{$model->url}"?></span>
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

    <div class="row">
        <div class="col-xs-12">
            <?= $form->field($model, 'seo_text')->widget(Imperavi::class) ?>
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
