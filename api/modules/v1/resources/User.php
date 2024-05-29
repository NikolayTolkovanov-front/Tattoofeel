<?php

namespace api\modules\v1\resources;

use common\models\UserClientProfile;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class User extends \common\models\UserClient
{
    public function fields()
    {
        return ['id', 'username', 'email', 'created_at'];
    }

//    public function extraFields()
//    {
//        return ['userProfile'];
//    }

    public function extraFields(){

        return [
            'profile' => function($item){
                return [
                    'age' => $item->profile->full_name
                ];
            }
        ];
    }
}
