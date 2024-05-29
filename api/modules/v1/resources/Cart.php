<?php

namespace api\modules\v1\resources;

use common\models\Product;
use common\models\UserClientOrder_Product;

class Cart extends UserClientOrder_Product
{
    public function fields()
    {
        return [
            'id',
            'order_id',
            'product_id',
            'count',
            'price',
            'currency_iso_code',
            'is_gift',
        ];
    }

//    public function extraFields()
//    {
//        return ['product_ms_id'];
//    }

//    public function extraFields(){
//
//        return [
//            'profile' => function($item){
//                return [
//                    'age' => $item->profile->full_name
//                ];
//            }
//        ];
//    }
}
