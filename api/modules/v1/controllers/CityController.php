<?php

namespace api\modules\v1\controllers;

use api\errors\ErrorMsg;
use common\models\DeliveryCity;
use common\models\SdekPvz;
use yii\rest\ActiveController;
use yii\web\HttpException;

class CityController extends ActiveController
{
    public $modelClass = 'common\models\DeliveryCity';

    public $strict = 1;
    public $maxLimit = 100;
    public $defaultLimit = 25;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    /**
     * Declare actions supported by APIs
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);

        return $actions;
    }

    /**
     * Declare methods supported by APIs
     */
    protected function verbs()
    {
        return [
            //'view' => ['GET'],
            //'index' => ['GET'],
            'get-by-name' => ['POST'],
            'get-sdek-pvz' => ['GET'],
        ];
    }

    public function actionGetByName()
    {
        $term = \Yii::$app->request->post('search', '');
        if (empty($term)) {
            throw new HttpException(404);
        }

        $strict = \Yii::$app->request->post('strict', $this->strict);

        $limit = \Yii::$app->request->post('limit', $this->defaultLimit);
        if ($limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        } elseif ($limit <= 0) {
            $limit = $this->defaultLimit;
        }

        $cities = DeliveryCity::find()
            //->where(['like', 'city_full', ($strict ? '' : '%').$term.'%', false])
            ->where(['like', 'city', ($strict ? '' : '%').$term.'%', false])
            ->orderBy(['city' => SORT_ASC])
            ->limit($limit)
            ->all();

        $data = array();
        if ($cities) {
            foreach ($cities as $city) {
                $data['cities'][] = array(
                    //'id' => $city->id,
                    'sdek_id' => $city->sdek_id,
                    'ms_id' => $city->ms_id,
                    'city' => $city->city,
                    'area' => $city->area,
                    'region' => $city->region,
                    'country' => $city->country,
                    'full_name' => $city->city.($city->area ? ', '. $city->area : '').($city->region ? ', '. $city->region : '').($city->country ? ', '. $city->country : ''),
                );
            }
        }

        if (!count($data)) {
            throw new HttpException(404);
        }

        return $data;
    }

    public function actionGetSdekPvz($sdek_id)
    {
        $pvz = SdekPvz::find()->where(['sdek_id' => intval($sdek_id)])->all();

        $data['sdek_id'] = intval($sdek_id);
        $data['pvz_count'] = count($pvz);
        foreach ($pvz as $item) {
            $data['pvz'][] = unserialize($item->xml);
        }

        if (!isset($data['pvz'])) {
            return ErrorMsg::customErrorMsg(400, "Пункты выдачи для населенного пункта не найдены");
        }

        return $data;
    }
}
