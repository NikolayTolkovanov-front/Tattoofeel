<?php

namespace api\modules\v1\resources;

use common\models\ProductAttachment;
use common\models\ProductCategory;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class Product extends \common\models\Product implements Linkable
{
    public function fields()
    {
        return [
            'ms_id',
            'slug',
            'title',
            'body',
            'body_short',
            'thumbnail_path' => function($model) {
                return \Yii::getAlias("@storageSourceUrl").'/'.$model->thumbnail_path;
            },
            'status',
            'is_main_in_config',
            'category_ms_id',
            'category' => function($model) {
                return ProductCategory::find()
                    ->select(['ms_id', 'slug', 'title', 'status'])
                    ->where(['ms_id' => $model->category_ms_id])
                    ->asArray()
                    ->all();
            },
            'config_ms_id',
            'manufacturer',
            'weight',
            //'amount',
            //'min_amount',
            'article',
            'brand_id',
            'title_short',
            'alt_desc',
            'warranty',
            'length',
            'width',
            'height',
            'is_fixed_amount',
            'is_oversized',
            'is_discount',
        ];

    }

    /**
     * Returns a list of links.
     *
     * @return array the links
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['product/view', 'id' => $this->ms_id], true)
        ];
    }
}
