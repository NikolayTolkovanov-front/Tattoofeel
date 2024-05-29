<?php

namespace frontend\widgets\products\plist;

use common\models\Product;
use common\models\ProductPrice;
use yii\base\Widget;

class ProductsList extends Widget
{

    public $inStock = false;
    public $sorted = false;
    public $emptyListShow = true;
    public $dataProvider = null;
    public $linkLoadMore = null;
    public $title = '';
    public $isAjax = null;
    public $hasFilter = false;
    public $brandPage = false;
    public $pagePost = 0;

    public function init()
    {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $models = $this->dataProvider->getModels();

        $productIds = $config_ms_ids = [];
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
        $params = [
            'id' => $this->id,
            'linkLoadMore' => $this->linkLoadMore,
            'dataProvider' => $this->dataProvider,
            'emptyListShow' => $this->emptyListShow,
            'sorted' => $this->sorted,
            'inStock' => $this->inStock,
            'hasFilter' => $this->hasFilter,
            'brandPage' => $this->brandPage,
            'pagePost' => $this->pagePost,

            'models' => $models,
            'isConfigInStockByMsIds' => $isConfigInStockByMsIds,
            'mappedRetailPrices' => $mappedRetailPrices,
        ];

        if ($this->isAjax) {
            return $this->render('_list-items', $params);
        }

        return $this->render('_list', $params);
    }
}
