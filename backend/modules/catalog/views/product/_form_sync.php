<?php

use backend\widgets\view\Collapse;
use yii\helpers\Url;

?>
<br />
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'revised')->checkbox() ?>
        <?= $form->field($model, 'disable_sync')->checkbox() ?>
        <?= $form->field($model, 'ms_id')->textInput() ?>
        <a href="<?= Url::to(['sync-product-one', 'ms_id' => $model->ms_id]) ?>" class="btn btn-success">
            Синхронизировать
        </a>
    </div>
    <div class="col-xs-8">
        <?= $form->field($model, 'disable_sync_prop__prepend')->dropDownList(
            \common\models\Product::getSyncPropAllWithLabel(),
            ['multiple' => true, 'style' => ['min-height' => '200px']]) ?>
    </div>
</div>

<?php if (!$model->isNewRecord) { ?>
<br />
<?php Collapse::begin([
    'title' => $model->getAttributeLabel('error'),
    'open' => $model->error
]) ?>
    <pre><?php print_r(json_decode($model->error, true)); ?></pre>
<?php Collapse::end() ?>

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
