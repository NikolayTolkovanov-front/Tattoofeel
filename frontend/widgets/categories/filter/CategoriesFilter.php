<?php

namespace frontend\widgets\categories\filter;

use common\models\Brand;
use frontend\models\Product;
use common\models\ProductCategory;
use common\models\ProductFilters;
use common\models\ProductFiltersCategory;
use common\models\TypeEq;
use yii\base\Widget;

class CategoriesFilter extends Widget
{

    public $category = null;
    public $brand = false;
    public $filterBrands = null;
    public $minMaxPrices = array(0,500000);
    public $discount = false;
    public $search = false;
    public $arFilter = array();

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {

        $filterCatCat = '{{%product_filters_category_product_category}}';

        if ($this->brand)
            $params = [
                'filterBrands' => $this->filterBrands
            ];
        else
            $params = [
                'cat_ms_id' => !is_null($this->category) ? $this->category->ms_id : '',
                'minMaxPrices' => $this->minMaxPrices,
                'discount' => $this->discount,
                'search' => $this->search,
                'category' => ProductCategory::find()
                    ->order()
                    ->published()
//                    ->innerJoin(Product::tableName(),
//                        Product::tableName() .".category_ms_id = ". ProductCategory::tableName() .".ms_id")
//                    ->andWhere(Product::queryPublished())
                    ->all(),

                'filterBrands' => Brand::find()->select([
                        Brand::tableName().'.title',
                        Brand::tableName().'.slug',
                        Brand::tableName().'.id']
                    )
                    ->published()
                    ->innerJoin(Product::tableName(),
                        Product::tableName() .".brand_id = ". Brand::tableName() .".slug")
                    ->innerJoin(ProductCategory::tableName(),
                        Product::tableName() .".category_ms_id = ". ProductCategory::tableName() .".ms_id")
                    ->andWhere([ProductCategory::tableName().".ms_id" => $this->category->ms_id])
                    ->andWhere(Product::queryPublished())
                    ->orderBy([Brand::tableName().'.title' => SORT_ASC])
                    ->all(),

                'filterManufacturer' => Product::find()
                    ->select(['manufacturer'])
                    ->prepareConfig()
                    ->distinct()
                    ->innerJoin(ProductCategory::tableName(),
                        Product::tableName() .".category_ms_id = ". ProductCategory::tableName() .".ms_id")
                    ->andWhere([ProductCategory::tableName().".ms_id" => $this->category->ms_id])
                    ->published()
                    ->orderBy(['manufacturer' => SORT_ASC])
                    ->all(),


                'filtersCommon' => ProductFiltersCategory::find()
                    ->innerJoin($filterCatCat,
                        $filterCatCat.".product_filters_category_id = " .ProductFiltersCategory::tableName() .".id"
                    )
                    ->andWhere([$filterCatCat.".product_category_id" => $this->category->id])
                    ->andWhere([ProductFiltersCategory::tableName().'.status' => ProductFiltersCategory::STATUS_PUBLISHED])
                    ->all(),
                'arFilter' => $this->arFilter,
            ];

        return $this->render($this->brand ? 'brand' : 'index', $params);
    }
}
