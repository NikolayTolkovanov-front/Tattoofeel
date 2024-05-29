<?php

namespace api\modules\v1\resources;

use common\models\DeliveryCity;

class City extends DeliveryCity
{
    public function fields()
    {
        return [
            'id',
            'sdek_id',
            'ms_id',
            'city',
            'area',
            'region',
            'country',
            'city_full',
            'pvz_code',
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
