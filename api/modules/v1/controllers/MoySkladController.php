<?php

namespace api\modules\v1\controllers;

use common\moy_sklad\Client;
use common\moy_sklad\entities\Invoiceout;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\NotAcceptableHttpException as NotAcceptable;

use Yii;

class MoySkladController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ]
        ];

        return $behaviors;
    }

    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * @return array
     */
    public function actionOrderCreate()
    {
        return ['order' => 'create'];
    }

    /**
     * @return array
     */
    public function actionOrderUpdate()
    {
        return ['order' => 'update'];
    }

    /**
     * @return array
     */
    public function actionOrderDelete()
    {
        return ['order' => 'delete'];
    }

    /**
     * @return array
     */
    public function actionCounterPartyCreate()
    {
        return ['counter_party' => 'create'];
    }

    /**
     * @return array
     */
    public function actionCounterPartyUpdate()
    {
        return ['counter_party' => 'update'];
    }

    /**
     * @return array
     */
    public function actionCounterPartyDelete()
    {
        return ['counter_party' => 'delete'];
    }

    public function actionClientCreate()
    {
        $payload = Yii::$app->request->post();
        $identity = Yii::$app->user->getIdentity();
        $profile = $identity->getProfile();

        $data = [
            'name' => $payload['name'],
            'organization' => [
                'id' => env('MS_ID_ORG_AT_BUY')
            ],
            'counterparty' => [
                'id' => $profile->client_ms_id
            ]
        ];

        try {
            $msClient = new Client();
            $response = $msClient->send(new Invoiceout($data));
        } catch (NotAcceptable $ex) {
            $response = [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        } catch (\Exception $ex) {
            $response = [
                'success' => false
            ];
        }

        return $response;
    }
}
