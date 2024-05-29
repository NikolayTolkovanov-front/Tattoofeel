<?php

namespace frontend\modules\api\controllers;

use frontend\modules\lk\models\Delivery;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;

class DeliveryController extends _Controller
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
     * @SWG\Get(
     *     path="/delivery/get-cities/?q={q}&ds={ds}",
     *     tags={"Доставка"},
     *     summary="Получить список городов для доставки.",
     *     @SWG\Parameter(
     *         in="query",
     *         name="q",
     *         type="string",
     *         required=true,
     *         description="Фрагмент названия города из автозаполения.",
     *     ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="ds",
     *          type="string",
     *          required=true,
     *          description="Код службы доставки, например cdek.",
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ с кучей полей.",
     *         @SWG\Schema(ref="#/definitions/ResponseWithData"),
     *     )
     * )
     */
    public function actionGetCities()
    {
        $term = $this->request('q');
        if (empty($term)) {
            return $this->success(false,'А где "q" ?');
        }

        $ds = $this->request('ds');
        if (empty($ds)) {
            return $this->success(false,'А где "ds" ?');
        }

        $response = \Yii::$app->runAction('lk/default/get-cities', [
            'term' => $term,
            'ds' => $ds
        ]);

        return $this->success(true, null, $response);
    }

    /**
     * @SWG\Get(
     *     path="/delivery/get-courier-intervals/",
     *     tags={"Доставка"},
     *     summary="Получить список временных интервалов для курьера.",
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ с кучей полей.",
     *         @SWG\Schema(ref="#/definitions/ResponseWithData"),
     *     )
     * )
     */
    public function actionGetCourierIntervals()
    {
        $keyStorage = \Yii::$app->keyStorageApp;
        $hide_hours = (int) $keyStorage->get('courier_hours_hide_today');
        $today = 'today_'. $keyStorage->get('courier_from_time_id_3') .'_'. $keyStorage->get('courier_to_time_id_3');
        $tomorrow_1 = 'tomorrow_'. $keyStorage->get('courier_from_time_id_1') .'_'. $keyStorage->get('courier_to_time_id_1');
        $tomorrow_2 = 'tomorrow_'. $keyStorage->get('courier_from_time_id_2') .'_'. $keyStorage->get('courier_to_time_id_2');
        $tomorrow_3 = 'tomorrow_'. $keyStorage->get('courier_from_time_id_3') .'_'. $keyStorage->get('courier_to_time_id_3');

        return $this->success(true, null, [
            'hide_hours' => $hide_hours,
            'intervals' => [
                'today' => $today,
                'tomorrow_1' => $tomorrow_1,
                'tomorrow_2' => $tomorrow_2,
                'tomorrow_3' => $tomorrow_3,
            ]
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/delivery/get-courier-price/?interval={interval}&order_id={order_id}",
     *     tags={"Доставка"},
     *     summary="Получить цену для временного интервала.",
     *     @SWG\Parameter(
     *         in="query",
     *         name="interval",
     *         type="string",
     *         required=true,
     *         description="ИД интервала.",
     *     ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="order_id",
     *          type="number",
     *          required=false,
     *          description="ИД заказа.",
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ с кучей полей.",
     *         @SWG\Schema(ref="#/definitions/ResponseWithData"),
     *     )
     * )
     */
    public function actionGetCourierPrice($interval, $order_id = 0)
    {
        $price = (new Delivery())->getCourierClientPrice($interval, $order_id);

        return $this->success(true, null, [
            'price' => $price
        ]);
    }
}
