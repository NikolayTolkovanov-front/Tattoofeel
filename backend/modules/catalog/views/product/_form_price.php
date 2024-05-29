
<?php
use common\models\Currency;
use yii\helpers\ArrayHelper;

/**
 * @var $prices      common\models\ProductPrice
 */
?>
<br />
<div class="row">
    <?php foreach($prices as $price) { ?>
        <div class="col-xs-12"><strong><?= $price->priceTemplate->title ?></strong></div>
        <div class="col-xs-6">
            <?= $form->field($price, "[$price->id]formatPrice")->textInput()->label(false) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($price, "[$price->id]currency_isoCode")->textInput(['disabled'=>true])->label(false) ?>
            <?php /* $form->field($price, "[$price->id]currency_isoCode")->dropDownList(
                ArrayHelper::map(Currency::find()->all(),'code_iso','fullName'),['disabled'=>true])->label(false) */ ?>
        </div>
    <?php } ?>
</div>
