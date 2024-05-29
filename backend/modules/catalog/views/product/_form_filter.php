<?php

use yii\helpers\ArrayHelper;

?>
<br />
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'productFilters')->dropDownList(
            ArrayHelper::map(
                    isset($model->category->productFilters) ? $model->category->productFilters : [],
                    'id', 'title', 'category.title'
            ),
            ['multiple' => true,'style'=>'height: 500px']
        ) ?>
    </div>
</div>

