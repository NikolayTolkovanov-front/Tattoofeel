<?php

use backend\widgets\form\Imperavi;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\productPriceTemplate
 * @var $categories common\models\productPriceTemplate[]
 */

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]); ?>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton(
            $model->isNewRecord ?
                t_b('Создать') :
                t_b('Редактировать'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    ) ?>
</div>

<?php ActiveForm::end() ?>
