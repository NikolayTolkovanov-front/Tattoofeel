<?php

use backend\widgets\form\DateTime;
use yii\helpers\ArrayHelper;
use common\models\ProductCategory;
use yii\helpers\Html;

/**
 * @var $groups
 * @var $model \common\models\Coupons
 * @var $form
 */

$links = $model->getClientGroupsArray();
?>
<br />
<button class="choose-all-groups" type="button">Выбрать все</button>
<script>
    window.addEventListener("DOMContentLoaded", function(){
        jQuery(function($){
            $('body').on('click', '.choose-all-groups', function(){
                $('.group-checks').prop('checked', !$('.group-checks').prop('checked')).change();
            });
            $('body').on('change', '.group-checks', function(){
                let val = []
                $('.group-checks').each(function () {
                    if ($(this).prop("checked") === true) {
                        val.push($(this).attr("value"))
                    }
                })
                val = val.join(',')
                $('#coupons-client_groups').val(val)
            });
        });
    });
</script>
<div class="row" style="padding-top: 20px;">
    <?php foreach($groups as $group) { ?>
        <div class="col-xs-4">
            <?= Html::checkbox('client_groups_dump[]', in_array($group['id'], $links), array(
                    'class'=>'group-checks',
                    'label'=>$group['name'],
                    'value'=>$group['id'],
                )) ?>
        </div>
    <?php } ?>
</div>
<div style="opacity: 0; pointer-events: none;">
    <?= $form->field($model, 'client_groups')->textInput() ?>
</div>
<br />
