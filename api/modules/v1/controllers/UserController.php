<?php

namespace api\modules\v1\controllers;

use common\models\UserClient;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use yii\web\HttpException;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                //HttpBasicAuth::class,
                HttpBearerAuth::class,
                //HttpHeaderAuth::class,
                //QueryParamAuth::class
            ]
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class
            ]
        ];
    }

    /**
     * @return UserClient|null|\yii\web\IdentityInterface
     * @throws \Throwable
     */
    public function actionIndex()
    {
        //$resource = new UserClient();
        //$resource->load(\Yii::$app->user->getIdentity()->attributes, '');

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            //$data['id'] = $identity->id;
            //$data['token'] = $identity->auth_key;
            //$data['user_status'] = $identity->status;
            $data['username'] = $identity->username;
            $data['email'] = $identity->email;

            $profile = $identity->getProfile();
            if ($profile) {
                $data['ms_id'] = $profile->client_ms_id;
                $data['full_name'] = $profile->full_name;
                $data['phone'] = $profile->phone;
                $data['phone_1'] = $profile->phone_1;
                $data['address_delivery'] = $profile->address_delivery;
                $data['address_comment'] = $profile->address_comment;
                $data['link_vk'] = $profile->link_vk;
                $data['link_inst'] = $profile->link_inst;
                $data['sale_ms_id'] = $profile->sale_ms_id;
                $data['sale_brands'] = $profile->sale_brands ? json_decode($profile->sale_brands): null;
                $data['ms_bonus'] = $profile->ms_bonus;
                $data['ms_owner'] = $profile->ms_owner;
                $data['ms_owner_name_at_site'] = $profile->ms_owner_name_at_site;
                $data['ms_owner_vk'] = $profile->ms_owner_vk;
                $data['ms_owner_whatsapp'] = $profile->ms_owner_whatsapp;
            }
            //$data['profile'] = $identity->getProfile();
        } else {
            throw new HttpException(404);
        }

        return  $data;
    }
}
