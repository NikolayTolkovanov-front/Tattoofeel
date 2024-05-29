<?php
use yii\helpers\Html;
/** @var \common\models\Coupons $model */
$gifts = [];
$giftsModels = \common\models\ProductGift::find()->where([
    'coupon_id' => $model->id,
]);
foreach ($giftsModels->all() as $giftsModel) {
    $gifts[] = $giftsModel->product_id.':'.$giftsModel->quantity;
}
$gifts = implode(PHP_EOL, $gifts);
?>
<br />
<div class="row">
    <div class="col-xs-12">
        <?=Html::textarea('gifts', $gifts, [
            'placeholder' => 'Введите ID товаров и количество в формате: id_товара:количество Один товар - одна строка',
            'class' => 'form-control',
            'style' => 'height: 400px;',
        ])?>
    </div>
</div>
<br />
