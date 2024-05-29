<?php

use backend\widgets\form\DateTime;
use backend\widgets\view\Collapse;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\UserClientOrder;
use common\models\UserClient;
use yii\helpers\Url;
use yii\jui\AutoComplete;

/* @var $this yii\web\View */
/* @var $model */
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
            <div class="col-xs-3">
                <?php echo $form->field($model, 'id')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-3">
                <?php echo $form->field($model, 'order_ms_id')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-3">
                <?php echo $form->field($model, 'order_ms_number')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-xs-3">
                <br />
                <?= $form->field($model, 'isCart')->checkbox(['disabled' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'status_pay')->dropDownList(UserClientOrder::statusesPay()) ?>
            </div>
            <div class="col-xs-6">
                <?php echo $form->field($model, 'date_pay')->widget(DateTime::class) ?>
                <?php /* $form->field($model, 'status_delivery')->dropDownList(UserClientOrder::statusesDelivery()) */ ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?php echo $form->field($model, 'date')->widget(DateTime::class) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'user_id')->dropDownList(
                    ArrayHelper::map(UserClient::find()->all(),'id','username'), ['prompt'=>'']
                ) ?>
            </div>
        </div>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, 'pay_id')->textInput(['disabled' => true]) ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'address_delivery')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, '_sum_delivery')->textInput(['disabled' => true]) ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'comment')->textarea() ?>
        </div>
    </div>
    <hr />
    <?php if (!$model->isNewRecord) { ?>
    <h4><b>Добавить продукт к заказу</b></h4>
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <?= AutoComplete::widget([
                        'name' => 'term',
                        'value' => Yii::$app->request->post('term'),
                        'options' => [
                            'placeholder' => "Название или код товара...",
                            'id' => 'product-search',
                        ],
                        'clientOptions' => [
                            'source' => Url::to(['search']),
                            'minLength' => '2',
                            'autoFocus' => true,
                            'classes' => [
                                "ui-autocomplete-input" => "form-control"
                            ]
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="col-xs-6 text-right">
                <h4>
                    Итого: <b>
                        <span class="op-main-total">0</span>
                        <span class="op-main-total-cur"></span>
                        <span class="op-main-total-cent"></span>
                        <span class="op-main-total-cur-cent"></span>
                    </b>
                </h4>
            </div>
        </div>

        <div class="order-products" id="order-products">
            <input type="hidden" name="UserClientOrder[products]" value="null" />
            <?php foreach($model->linkProducts as $link) {?>

                <?= $this->render('_form-product',[
                    'id' => $link->product->id,
                    'title' => $link->product->title,
                    'price' => $link->product->clientPrice->getCartPrice($link->count),
                    'count' => $link->count
                ]);?>
            <?php } ?>
        </div>

        <template id="order-products-row">
            <?= $this->render('_form-product',[
                'id' => '',
                'title' => '',
                'price' => '',
                'count' => 1
            ]);?>
        </template>
    <?php } else { ?>
        <p>Для добавления продуктов создайте ордер</p>
    <?php } ?>
    <br />
    <?php if (!$model->isNewRecord) { ?>
        <?php Collapse::begin([
            'title' => 'Создал/обновил',
            'open' => false
        ]) ?>        <div class="row">
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
            <?= Html::a(
                'Выгрузить в МС',
                Url::to(['sync-admin', 'id' => $model->id]),
                ['class' => 'btn btn-success']
            ) ?>
        </div>
    <?php ActiveForm::end() ?>

</div>
