<?php

namespace frontend\widgets\products\row;

use common\models\Product;
use common\models\ProductPrice;
use yii\base\Widget;

class ProductsRow extends Widget
{

    public $title = '';
    public $dataProvider = null;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $productIds = $config_ms_ids = [];
        $models = $this->dataProvider->getModels();
        foreach($models as $model) {
            $productIds[] = $model->id;
            $config_ms_ids[] = $model->config_ms_id;
        }
        $isConfigInStockByMsIds = [];
        if (count($config_ms_ids) > 0) {
            $isConfigInStockByMsIds = Product::getIsConfigInStockByMsIds($config_ms_ids);
        }
        $mappedRetailPrices = [];
        if (count($productIds) > 0) {
            $mappedRetailPrices = ProductPrice::getByProductIdsAndTemplate(
                $productIds,
                Product::PRICE_TEMPLATE_RETAIL_PRICE
            );
        }

        return $this->render('index', [
            'title' => $this->title,
            'models' => $models,
            'mappedRetailPrices' => $mappedRetailPrices,
            'isConfigInStockByMsIds' => $isConfigInStockByMsIds,
        ]);
    }
}
