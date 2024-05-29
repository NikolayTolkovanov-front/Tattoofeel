<?php

/**
* @var $title
* @var $models Product[]
* @var $mappedRetailPrices array
* @var $isConfigInStockByMsIds array
 */

use common\models\Product;
use frontend\widgets\common\Icon;
?>
<div class="block-section">
    <div class="block-section__head">
        <h2 class="h1 center"><?= $title ?></h2>
    </div>
    <div class="block-section__list">
        <div class="product-row">
            <div class="product-row__wrap">
                <div class="product-row-list">
                    <?php foreach($models as $model) {
                        $retailPrices = $mappedRetailPrices[$model->id];
                        $isConfigInStock = $isConfigInStockByMsIds[$model->config_ms_id];
                        if ($model->isConfig && $model->isMainConfig) {
                            echo $this->render(
                                    '_item-is-config',
                                    [
                                        'model' => $model,
                                        'retailPrices' => $retailPrices,
                                        'isConfigInStock' => $isConfigInStock,
                                    ]
                            );
                        } else {
                            echo $this->render(
                                    '_item',
                                    [
                                        'model' => $model,
                                        'retailPrices' => $retailPrices,
                                        'isConfigInStock' => $isConfigInStock,
                                    ]
                            );
                        }
                    } ?>
                </div>
                <div class="product-row-list"></div>
            </div>
            <span class="product-row__prev">
                <?= Icon::widget(['name' => 'arw_small','width'=>'8px','height'=>'15px',
                    'options'=>['fill'=>"#f8cd4f"]
                ]) ?>
            </span>
            <span class="product-row__next">
                <?= Icon::widget(['name' => 'arw_small','width'=>'8px','height'=>'15px',
                    'options'=>['fill'=>"#f8cd4f"]
                ]) ?>
            </span>
            <ul class="product-row__dots"></ul>
        </div>
    </div>
</div>
