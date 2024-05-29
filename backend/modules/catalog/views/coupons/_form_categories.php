<?php

use backend\widgets\form\DateTime;
use yii\helpers\ArrayHelper;
use common\models\ProductCategory;

/**
 * @var $categories common\models\ProductCategory[]
 */

$links = $model->getCategories()->indexBy('id')->column();
?>
<br />
<button class="choose-all-cats" type="button">Выбрать все</button>
<script>
    window.addEventListener("DOMContentLoaded", function(){
        jQuery(function($){
            $('body').on('click', '.choose-all-cats', function(){
                $('.cat-checks').prop('checked', !$('.cat-checks').prop('checked'));
            });
        });
    });
</script>
<div class="row">
    <?php foreach($categories as $category) { ?>
        <div class="col-xs-4">
            <?= $form->field($category, "id[$category->id]")->checkbox(['checked' => in_array($category->id, $links), 'class' => "cat-checks"])->label($category->title) ?>
        </div>
    <?php } ?>
</div>
<br />
