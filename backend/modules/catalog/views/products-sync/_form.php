<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use backend\widgets\view\Collapse;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\productSync
 */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'status')->checkbox( ['disabled'=>true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'date')->textInput(['disabled'=>true]) ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'author')->textInput(['disabled'=>true,'value' => $model->sender ? $model->sender->username : null]) ?>
    </div>
</div>

<!--
<?php Collapse::begin([
   'title' => $model->getAttributeLabel('products')
]) ?>
    <?= $form->field($model, 'products')->textarea([
            'disabled'=>true,
            'style' => ['min-height' => '300px']
    ])->label(false) ?>
<?php Collapse::end() ?>
-->

<?php Collapse::begin([
    'title' => t_b('Логи/ошибки'),
    'open' => $model->error,
]) ?>
    <pre><?php print_r(json_decode($model->error, true)); ?></pre>
<?php Collapse::end() ?>

<div class="form-group">
    <?= Html::a(
        t_b('Закрыть'),
        ['index'],
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    ) ?>
</div>

<?php ActiveForm::end() ?>
