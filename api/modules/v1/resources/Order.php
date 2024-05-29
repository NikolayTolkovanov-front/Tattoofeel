<?php

namespace api\modules\v1\resources;

use common\models\UserClientOrder;

class Order extends UserClientOrder
{
    public function fields()
    {
        return [
            'id',
            'user_id',
            'order_ms_id',
            'date',
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
