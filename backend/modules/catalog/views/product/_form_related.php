<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\jui\AutoComplete;

?>
<br />
<div class="row">
    <div class="col-xs-12">
        <?php /*
        <?= $form->field($model, 'productRelated')->dropDownList(
            ArrayHelper::map(
                    \common\models\Product::find()->all(),
                    'id', 'title'
            ),
            ['multiple' => true,'style'=>'height: 500px']
        ) ?>
        */ ?>

        <?php if (!$model->isNewRecord) { ?>
            <h4><b>Добавить продукт к списку</b></h4>
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <?= AutoComplete::widget([
                            'name' => 'term',
                            'value' => Yii::$app->request->post('term'),
                            'options' => [
                                'placeholder' => "Название или код товара...",
                                'id' => 'product-search-rel',
                            ],
                            'clientOptions' => [
                                    //todo global action
                                'source' => Url::to(['/client/orders/search']),
                                'minLength' => '2',
                                'autoFocus' => true,
                                'classes' => [
                                    "ui-autocomplete-input" => "form-control"
                                ]
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>

            <div class="product-related" id="product-related">
                <input type="hidden" name="Product[productRelated]" value="null" />
                <?php foreach($model->productRelated as $r) {?>

                    <?= $this->render('_form_related-product',[
                        'id' => $r->id,
                        'title' => $r->title
                    ]);?>
                <?php } ?>
            </div>

            <template id="product-related-temp">
                <?= $this->render('_form_related-product',[
                    'id' => '',
                    'title' => ''
                ]);?>
            </template>
        <?php } else { ?>
            <p>Для добавления списока создайте продукт</p>
        <?php } ?>
        <br />
        <hr />

    </div>
</div>

