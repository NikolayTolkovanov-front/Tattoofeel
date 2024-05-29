<?php
use \yii\helpers\Html;
?>
<div class="product-card-data__props">
    <span><?= $model->mainConfig && $model->mainConfig->config_name ? $model->mainConfig->config_name : 'Конфигурации'?>:</span>
    <div class="product-card-props <?= $count <= 3 ? '_3' : ( $count == 4 ? '_4' : '' )?>">

        <?php foreach($configs as $c) {
            echo Html::a(Html::tag('span', $c->titleShortShort), $c->route,
                [
                    'class' => [
                        'pcp-item',
//                        $c->ms_id == $model->ms_id ?
//                            '-act' :
//                            ( $c->amountIndex == 0 ? '-dis' : null )
                        $c->ms_id == $model->ms_id ? '-act' : null,
                        $c->amountIndex == 0 ? '-dis' : null,
                    ],
                    'data-pjax' => 0,
                    'data-id' => $c->id,
                    'title' => $c->config_decrypt
                ]
            );
        } ?>

        <?php /*
        <span class="-dis"><span>60 мл</span></span>
        <span class="-act"><span>80 мл</span></span>
        <span><span>100 мл</span></span>
        */ ?>

    </div>
</div>
