<?php

use backend\widgets\form\DateTime;
use yii\helpers\ArrayHelper;
use common\models\Product;

//$links = $model->getProducts()->indexBy('id')->column();
$links = $model->getProducts()->indexBy('id')->column();
?>

<br />
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'products')->textarea([
            'max-length' => true,
            'style' => 'height: 400px;resize: vertical;',
            'value' => implode(',', $links),
        ])->label('Введите ID товаров через запятую (напр. 2,123,46,561):') ?>
    </div>
</div>
<br />
