<?php

namespace api\modules\v1\resources;

use common\models\DeliveryTypes;

class Delivery extends DeliveryTypes
{
    public function fields()
    {
        return [
            'id',
            'title',
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
