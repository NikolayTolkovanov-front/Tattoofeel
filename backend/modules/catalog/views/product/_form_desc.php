
<?php

use backend\widgets\form\Imperavi;

/**
 * @var $prices      common\models\ProductPrice
 */

?>
<br />
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'body')->widget(Imperavi::class) ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'body_short')->textarea([
                'max-length' => true,
                'style'=>'height: 400px'
        ]) ?>
    </div>
</div>
