<?php

use backend\widgets\form\Imperavi;
use backend\widgets\form\upload\Upload;
use backend\widgets\view\Collapse;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use common\models\ProductFiltersCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\productCategory
 * @var $categories common\models\productCategory[]
 */
$model->created_at = Yii::$app->formatter->asDateTime($model->created_at);
$model->updated_at = Yii::$app->formatter->asDateTime($model->updated_at);

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';

if ($model->isNewRecord)
    $model->status = 1;

$categoryOptions = \Yii::$app->getModule('catalog')
    ->CategoryList->getForSelection();

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]); ?>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'parent_id')->dropDownList($categoryOptions) ?>
        <?= $form->field($model, 'status')->checkbox() ?>
        <?= $form->field($model, 'disable_sync')->checkbox() ?>
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'ms_id')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'small_amount')->textInput() ?>
        <?= $form->field($model, 'large_amount')->textInput() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'body_short')->widget(Imperavi::class) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'thumbnail')->widget(Upload::class) ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'icon')->widget(Upload::class) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'productFiltersCategories')->dropDownList(
                ArrayHelper::map(ProductFiltersCategory::find()->all(), 'id', 'title'),
                ['multiple' => true]
        ) ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'order')->textInput() ?>
    </div>
</div>

<br />
<div class="row">
    <div class="col-xs-8">
        <div id="seo-snippet" class="seo-snippet">
            <a href="<?=Yii::$app->request->hostInfo."/catalog/{$model->slug}/"?>" target="_blank" class="seo-snippet-link">
                <h3 id="seo-snippet-title" class="seo-snippet-title"><?=$model->seo_title ?: 'Seo Заголовок'?></h3>
                <span id="seo-snippet-url" class="seo-snippet-url"><?=Yii::$app->request->hostInfo."/catalog/{$model->slug}/"?></span>
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
    <?php Collapse::begin([
        'title' => $model->getAttributeLabel('error'),
        'open' => $model->error
    ]) ?>
        <pre><?php print_r(json_decode($model->error, true)); ?></pre>
    <?php Collapse::end() ?>
<?php } ?>

<div class="form-group">
    <?php echo !$model->isNewRecord ? Html::submitButton( Yii::t('backend', 'Обновить'),
        ['class' => 'btn btn-primary', 'name' => 'update', 'value' => 1,
            'disabled' => (bool) Yii::$app->keyStorage->get('backend.productCat.sync.isStart')]) : null ?>
    <?php echo Html::submitButton( Yii::t('backend', 'Сохранить'),
        ['class' => 'btn btn-success', 'name' => 'save', 'value' => 1,
            'disabled' => (bool) Yii::$app->keyStorage->get('backend.productCat.sync.isStart')]) ?>
    <?php echo Html::a( Yii::t('backend', 'Отменить'), Url::to(['index']),
        ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end() ?>
