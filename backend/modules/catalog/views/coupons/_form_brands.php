<?php

use backend\widgets\form\DateTime;
use yii\helpers\ArrayHelper;
use common\models\Brand;

/**
 * @var $brands common\models\Brand[]
 */

$links = $model->getBrands()->indexBy('id')->column();
?>

<br />
<button class="choose-all-brands" type="button">Выбрать все</button>
<script>
    window.addEventListener("DOMContentLoaded", function(){
        jQuery(function($){
            $('body').on('click', '.choose-all-brands', function(){
                $('.brand-checks').prop('checked', !$('.brand-checks').prop('checked'));
            });
        });
    });
</script>
<div class="row">
    <?php foreach($brands as $brand) { ?>
        <div class="col-xs-4">
            <?= $form->field($brand, "id[$brand->id]")->checkbox(['checked' => in_array($brand->id, $links), 'class' => "brand-checks"])->label($brand->title) ?>
        </div>
    <?php } ?>
</div>
<br />
