<?php

use backend\widgets\view\Collapse;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\UserClient;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Brand;

/* @var $this yii\web\View */
/* @var $model backend\modules\client\models\UserClientForm */
/* @var $form yii\bootstrap\ActiveForm */

$model->created_at = $model->created_at ?
    Yii::$app->formatter->asDateTime($model->created_at) : '';
$model->updated_at = $model->updated_at ?
    Yii::$app->formatter->asDateTime($model->updated_at) : '';

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';

$model->client_created_at = $model->client_created_at ?
    Yii::$app->formatter->asDateTime($model->client_created_at) : '';
$model->client_updated_at = $model->client_updated_at ?
    Yii::$app->formatter->asDateTime($model->client_updated_at) : '';

$model->client_created_by = $model->authorClient->username??'';
$model->client_updated_by = $model->updaterClient->username??'';

?>

<div class="user-form">

    <?php $form = ActiveForm::begin() ?>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'phone')->textInput([
                        'maxlength' => true,
                        //'data-inputmask' => "'mask': '+7 (999) 999-99-99'"
                    ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'address_delivery')->textarea(['maxlength' => true,'style'=>'min-height:270px']) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'phone_1')->textInput([
                        'maxlength' => true,
                        //'data-inputmask' => "'mask': '+7 (999) 999-99-99'"
                ]) ?>
                <?= $form->field($model, 'link_inst')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'link_vk')->textInput(['maxlength' => true]) ?>
                <div class="row">
                    <div class="col-xs-9">
                        <?= $form->field($model, 'sale_ms_id')->textInput(['disabled' => true]) ?>

                        <?php
                            $allBrands = ArrayHelper::getColumn(Brand::find()->all(), 'slug');

                            foreach($model->getSalesBrandsArr() as $sale => $brands) {
                                $checkBrands = [];

                                foreach ($brands as $b) {
                                    if (in_array($b, $allBrands))
                                        $checkBrands[] = '<span style="color:green">' . $b . '</span>';
                                    else
                                        $checkBrands[] = '<span style="color:red; text-decoration: underline">' . $b . '</span>';
                                }

                                echo implode(', ',$checkBrands) ." ".$form->field($model, 'sb')->textInput([
                                        'disabled' => true,
                                        'value' => $sale
                                     ])->label(false);
                            }
                        ?>
                    </div>
                    <div class="col-xs-3">
                        <label>&nbsp;</label><br/>
                        <a class="btn btn-success" href="<?= Url::to(['profile-sync', 'id' => $model->id]) ?>">
                            Синхр.
                        </a>
                    </div>
                </div>
                <?= $form->field($model, 'client_ms_id')->textInput() ?>
            </div>
        </div>

    <?php if (!$model->isNewRecord) { ?>
        <?php Collapse::begin([
            'title' => 'Создал/обновил',
            'open' => false
        ]) ?>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'client_created_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'client_created_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'client_updated_at')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'client_updated_by')->textInput(['disabled' => true]) ?>
            </div>
        </div>
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
            <?= Html::submitButton(
                $model->isNewRecord ?
                    t_b('Добавить') :
                    t_b('Обновить'),
                [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                ]
            ) ?>
            <?= !$model->isNewRecord ? Html::a( t_b('Отмена'),
                ['index'],
                ['class' => 'btn btn-default']
            ) : '' ?>
        </div>
    <?php ActiveForm::end() ?>

</div>
