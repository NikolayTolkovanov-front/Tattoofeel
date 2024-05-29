<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.10.2020
 * Time: 12:01
 */

namespace frontend\modules\lk\models;


use common\components\cdek\CalculateTariffList;
use common\components\iml\Iml;
use common\components\pickPoint\PickPoint;
use common\models\DeliveryCity;
use common\models\SdekPvz;
use common\models\UserClientOrder;
use common\models\UserClientOrder_Product;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * @property array $countries Список стран, в которых осуществляется доставка
 */
class Delivery
{
    const DELIVERY_CDEK = 'cdek';
    const DELIVERY_IML = 'iml';
    const DELIVERY_PICK_POINT = 'pick_point';
    const PICKUP_FROM_BRATISLAVSKAYA = 'pickup_brat';
    const DELIVERY_COURIER = 'courier';

//    const DELIVERY_COURIER_FROM_09_00 = '5150c2e4-7693-11e7-7a6c-d2a90020a3c6';
//    const DELIVERY_COURIER_FROM_12_00 = '6223bf87-3cc2-11e4-296f-002590a28eca';
//    const DELIVERY_COURIER_FROM_15_00 = 'c7204708-a8f6-11e7-7a31-d0fd000950b3';
//    const DELIVERY_COURIER_TO_15_00 = 'bc1a4d6f-39c9-11e4-56bb-002590a28eca';
//    const DELIVERY_COURIER_TO_18_00 = '7ab5d81f-39c0-11e4-0757-002590a28eca';
//    const DELIVERY_COURIER_TO_21_00 = '88c1992c-4d33-11e8-9ff4-34e800253c9e';

    const CDEK_SD_DELIVERY_TYPE = 1; //Склад-Дверь
    const CDEK_SS_DELIVERY_TYPE = 2; //Склад - Склад
    const IML_SD_DELIVERY_TYPE = 1; //Склад-Дверь
    const IML_SS_DELIVERY_TYPE = 2; //Склад - Склад
    const PICKUP_FROM_WAREHOUSE = 3; //Самовывоз со склада
    const OUR_COURIER = 4; //Наш курьер

    const PICK_POINT_MAX_BOX = [60, 62, 65]; //Максимальный размер коробки

    private $countries = ['RU', 'BY', 'KZ', 'AM', 'KG'];

    /**
     * Список служб доставки
     * @return array
     */
    public function getDSList()
    {
        return [
            self::DELIVERY_CDEK => 'СДЭК',
            self::DELIVERY_IML => 'IML',
            self::DELIVERY_PICK_POINT => 'PickPoint',
            self::DELIVERY_COURIER => 'Courier',
        ];
    }

    /**
     * @param string $term
     * @return array
     */
    public function getCityByTerm($term = '', $strict = false)
    {
        $cities = DeliveryCity::find()
            ->where(['like', 'city_full', ($strict ? '' : '%').$term.'%', false])
            ->orderBy(['city' => SORT_ASC])
            ->limit(10)
            ->all();

        $arCity = array();
        if ($cities) {
            foreach ($cities as $city) {
                $arCity[] = array(
                    'id' => $city->sdek_id,
                    'postCodeArray' => '',
                    'cityName' => $city->city,
                    'areaName' => $city->area,
                    'regionId' => 0,
                    'regionName' => $city->region,
                    'countryId' => 0,
                    'countryName' => $city->country,
                    'countryIso' => '',
                    'name' => $city->city.($city->area ? ', '. $city->area : '').($city->region ? ', '. $city->region : '').($city->country ? ', '. $city->country : ''),
                );
            }
        }

        if (!empty($arCity)) {
            return [
                'status' => true,
                'data' => $arCity,
            ];
        }

        return [
            'status' => 'false',
            'msg' => 'Cities for service not found'
        ];
    }

//    /**
//     * @param string $term
//     * @return array
//     */
//    public function getCdekCityByTerm($term = '')
//    {
//
//        try {
//            $cities = file_get_contents("http://api.cdek.ru/city/getListByTerm/jsonp.php?q={$term}");
//        } catch (\Exception $e) {
//            try {
//                if (strpos($term, ',')) {
//                    $term = trim(explode(',', $term)[0]);
//                }
//                $cities = file_get_contents("http://api.cdek.ru/city/getListByTerm/jsonp.php?q={$term}");
//            } catch (\Exception $e) {
//                Yii::error($e->getMessage(), '_error');
//                return [
//                    'status' => false,
//                    'msg' => $e->getMessage(),
//                ];
//            }
//        }
//        $cities = json_decode($cities, true);
//
//        if (isset($cities['geonames'])) {
//            foreach ($cities['geonames'] as $key => $city) {
//                if (in_array($city['countryIso'], $this->countries) === false) {
//                    unset($cities['geonames'][$key]);
//                }
//            }
//            //Yii::info($cities['geonames'], 'test');
//
//            return [
//                'status' => true,
//                'data' => $cities['geonames'],
//            ];
//        }
//        return [
//            'status' => 'false',
//            'msg' => 'Cities for Cdek service not found'
//        ];
//
//    }

    /**
     * @param string $term
     * @return array
     */
    public function getImlCityByTerm($term = '')
    {
        $iml = new Iml();
        $cities = $iml->getCitiesByTerm($term);
//        Yii::warning($cities, 'test');
        if (!$cities['status']) {
            return $cities;
        } else {
            $cities = $cities['data'];
        }

        $result = [];
//        Yii::info($cities, 'test');
        if ($cities) {
            foreach ($cities as $info) {
                if ($info['city'] != $info['region_iml']) {
                    //В базе IML есть глючные записи. Например Смоленск. У одного region_iml - РОСЛАВЛЬ,
                    // у другого СМОЛЕНСК. Регионы IML (region_iml) совпадают с названием города
                    continue;
                }
                if ($info['area']) {
                    $name = $info['city'] . ', ' . $info['area'] . ', ' . $info['region'];
                } else {
                    $name = $info['city'] . ', ' . $info['region'];
                }

                array_push($result, [
                    'name' => $name,
                    'cityName' => $info['city'],
                    'id' => $info['region_iml']
                ]);
            }

//        Yii::info($result, 'test');

            $total_result = [
                'status' => true,
                'data' => $result,
            ];

            return $total_result;
        }

        return [];

    }

    /**
     * @param string $term
     * @return array
     */
    public function getPpCityByTerm($term = '')
    {
        $pp = new PickPoint();
        $cities = $pp->getCitiesByTerm($term);

        if (!$cities['status']) {
            return $cities;
        } else {
            $cities = $cities['data'];
        }

        $result = [];

        if ($cities) {
            foreach ($cities as $id => $address) {

                array_push($result, [
                    'name' => $address,
                    'cityName' => explode($address, ',')[0],
                    'id' => $id
                ]);
            }

//        Yii::info($result, 'test');

            return [
                'status' => true,
                'data' => $cities
            ];
        } else {
            return [
                'status' => false,
                'msg' => 'Cities for PickPoint not found',
            ];
        }
    }

    /**
     * Сумма доставки
     * @param $city_id
     * @return array
     */
    public function getCdekSum($city_id)
    {
        $goods = $this->getGoods();

        $storage_to_door_tariff_list = array_keys($this->getCdekTariffList($this::CDEK_SD_DELIVERY_TYPE));
        $storage_to_storage_tariff_list = array_keys($this->getCdekTariffList($this::CDEK_SS_DELIVERY_TYPE));

        $tariff_list = array_merge($storage_to_door_tariff_list, $storage_to_storage_tariff_list);

        $api = new CalculateTariffList([
            'receiverCityId' => $city_id,
            'tariffList' => $tariff_list,
            'goods' => $goods
        ]);

        $result = $api->calculate();

        /*
        Array
            (
                [status] => 1
                [data] => Array
                    (
                        [0] => Array
                            (
                                [tariffId] => 5
                                [status] =>
                                [result] => Array
                                    (
                                        [errors] => Array
                                            (
                                                [code] => 3
                                                [text] => Невозможно осуществить доставку по этому направлению при заданных условиях
                                            )

                                    )

                            )

                        [1] => Array
                            (
                                [tariffId] => 10
                                [status] => 1
                                [result] => Array
                                    (
                                        [price] => 220
                                        [deliveryPeriodMin] => 1
                                        [deliveryPeriodMax] => 1
                                        [tariffId] => 10
                                        [priceByCurrency] => 220
                                        [currency] => RUB
                                    )

                            )

                        [2] => Array
                            (
                                [tariffId] => 11
                                [status] => 1
                                [result] => Array
                                    (
                                        [price] => 310
                                        [deliveryPeriodMin] => 1
                                        [deliveryPeriodMax] => 1
                                        [tariffId] => 11
                                        [priceByCurrency] => 310
                                        [currency] => RUB
                                    )

                            )
                        ...
                    )

                [msg] => Array
                    (
                    )

            )
         */

        return $result;
    }

    public function getSdekSumAndPeriod($city_id, $tariffType = self::CDEK_SS_DELIVERY_TYPE, $order_id = 0)
    {
        $goods = $this->getGoods($order_id);

        $api = new CalculateTariffList([
            'receiverCityId' => $city_id,
            'tariffList' => array_keys($this->getCdekTariffList($tariffType)),
            'goods' => $goods
        ]);

        $arResult = $api->calculate();
        $result = false;
        if (isset($arResult['status']) && $arResult['status'] == 1) {
            if (isset($arResult['data']) && is_array($arResult['data'])) {
//                echo '<pre>';print_r($arResult);echo '</pre>';
//                die();
                $result = $this->getSdekTariffs($arResult['data'], $tariffType);
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Возвращает массив с действующими тарифами, минимальной ценой и периодами доставки для тарифов СДЭК.
     *
     * @param array $data
     * @param int $tariffType
     * @return array
     */
    private function getSdekTariffs($data, $tariffType = self::CDEK_SS_DELIVERY_TYPE)
    {
        $tariffNames = $this->getCdekTariffList($tariffType);
        $tariffs = array_keys($tariffNames);

        $result = array(
            'min_tariff_id' => 0,
            'min_tariff_name' => '',
            'sum_min' => 999999999,
            'period_min' => 0,
            'period_max' => 0,
            'tariffs' => array(),
        );

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['status'] == 1) {
                if (in_array($data[$i]['tariffId'], $tariffs)) {
                    $price = (float)$data[$i]['result']['price'];
                    if ($result['sum_min'] > $price) {
                        $result['sum_min'] = $price;
                        $result['period_min'] = $data[$i]['result']['deliveryPeriodMin'];
                        $result['period_max'] = $data[$i]['result']['deliveryPeriodMax'] + 1;
                        $result['min_tariff_id'] = $data[$i]['tariffId'];
                        $result['min_tariff_name'] = $tariffNames[$data[$i]['tariffId']];
                    }
                    $arr = $data[$i]['result'];
                    $arr['tariffName'] = $tariffNames[$data[$i]['tariffId']];
                    $arr['deliveryPeriodMax'] += 1;
                    $result['tariffs'][] = $arr;
                }
            }
        }

        return $result;
    }

    /**
     * Расчет суммы доставки
     * @param $city_code
     * @param string $iml_tariff (JOB)
     * @return array
     */
    public function getImlSum($city_code, $iml_tariff)
    {
        $goods = $this->getGoods();
        $all_param = [];

        foreach ($goods as $item) {
            $all_param['weight'] += $item['weight'];
            $all_param['volume_weight'] += $item['volume_weight'];
        }


        $all_param['volume'] = 1;
        /* https://iml.ru/legal/delivery-rules
        20.2.При тарификации в расчет берется большее из двух значений – физический или объемный вес, с округлением
        до полного килограмма в большую сторону. Формула объемного веса:
        ВЕСоб. = Д (см) х Ш (см) х В (см) / 5000
        Например, Заказ весом 3,2 кг, размера 30 см х 50 см х 10 см (ноутбук) будет тарифицироваться как:
        ВЕСфакт = 3,2 кг (округляем до 4 кг), ВЕСобъем = 30 х 50 х 10 /5000 = 3 (округляем до 3 кг).
        Максимум из (ВЕСфакт и ВЕСобъем )= 4 кг. */

        $iml = new Iml();
        $iml->setRequestData([
            'Job' => $iml_tariff,
            'RegionFrom' => 'МОСКВА',
            'RegionTo' => $city_code,
            'Weigth' => max($all_param['weight'], $all_param['volume_weight']),
            'Volume' => $all_param['volume'],
        ]);

        $price = $iml->calculatePrice();

        return $price;
    }

    public function getImlSumAndPeriod($city_name, $tariffType = self::IML_SS_DELIVERY_TYPE)
    {
        $result = false;

        $iml_city_code = $this->getImlCityCodeByCityName($city_name);
        if ($iml_city_code['status']) {
            $result = array(
                'min_tariff_id' => 0,
                'min_tariff_name' => '',
                'sum_min' => 999999999,
                'period_min' => null,
                'period_max' => 0,
                'tariffs' => array(),
            );

            if ($tariffType === self::IML_SD_DELIVERY_TYPE) {
                $tariffs = [
                    '24' => 'Доставка предоплаченого заказа',
                ];
            } elseif ($tariffType === self::IML_SS_DELIVERY_TYPE) {
                $tariffs = [
                    'С24' => 'Доставка на ПВЗ предоплаченного заказа',
                ];
            } else {
                $tariffs = [
                    '24' => 'Доставка предоплаченого заказа',
                    'С24' => 'Доставка на ПВЗ предоплаченного заказа',
                ];
            }

            $tariffs = $this->getImlTariffList('reduced_list');
            foreach ($tariffs as $tariff_code => $tariff_description) {
                $tariff = $this->getImlSum($iml_city_code['data'], $tariff_code);

//                echo '<pre>';print_r($tariff);echo '</pre>';
//                die();

                $item = array();
                if ($tariff['status']) {
                    $tariff = $tariff['data'];

                    $item['price'] = isset($tariff['Price']) && $tariff['Price'] ? ceil($tariff['Price']) : null;
                    $item['deliveryPeriodMin'] = null;
                    $item['deliveryPeriodMax'] = ceil((strtotime($tariff['DeliveryDate']) - time()) / (60 * 60 * 24)) + 1;
                    $item['tariffId'] = $tariff['Request']['Job'];
                    $item['tariffName'] = $tariff_description;

                    if (isset($tariff['PriceList'][0]['Price']) && $tariff['PriceList'][0]['Price']) {
                        $item['price'] = ceil($tariff['PriceList'][0]['Price']);
                    }

                    if (!is_null($item['price']) && $result['sum_min'] > $item['price']) {
                        $result['sum_min'] = $item['price'];
                        $result['period_max'] = $item['deliveryPeriodMax'];
                        $result['min_tariff_id'] = $item['tariffId'];
                        $result['min_tariff_name'] = $item['tariffName'];
                    }

                    $result['tariffs'][] = $item;
                } else {
                    $result = false;
                }
            }
        }

        return $result;
    }

    public function getPickPointSumAndPeriod($city_name, $tariffType = self::IML_SS_DELIVERY_TYPE)
    {
        $result = false;

        $pp_city_code = $this->getPpCityCodeByCityName($city_name);
        if ($pp_city_code['status'] && $pp_city_code['data']) {
            $result = array(
                'min_tariff_id' => 0,
                'min_tariff_name' => '',
                'sum_min' => 999999999,
                'period_min' => null,
                'period_max' => 0,
                'can_contain' => $this->canContain(self::DELIVERY_PICK_POINT),
                'tariffs' => array(),
            );


            $info = $this->getPpSum($pp_city_code['data']);

//            echo '<pre>';print_r($info);echo '</pre>';
//            die();

            if (isset($info['status']) && $info['status']) {
                foreach ($info['data']['Services'] as $tariff) {
                    $item['price'] = ceil($tariff['Tariff']);
                    $item['deliveryPeriodMin'] = (int)$info['data']['DPMin'];
                    $item['deliveryPeriodMax'] = (int)$info['data']['DPMax'] + 1;
                    $item['tariffId'] = $tariff['DeliveryMode'];
                    $item['tariffName'] = $tariff['Name'];
                    $result['tariffs'][] = $item;

                    if ($result['sum_min'] > $item['price']) {
                        $result['sum_min'] = $item['price'];
                        $result['period_min'] = $item['deliveryPeriodMin'];
                        $result['period_max'] = $item['deliveryPeriodMax'];
                        $result['min_tariff_id'] = $item['tariffId'];
                        $result['min_tariff_name'] = $item['tariffName'];
                    }
                }
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Расчет суммы доставки
     * @param $city_code
     * @return array
     */
    public function getPpSum($city_code)
    {
        $goods = $this->getGoods();
        $all_param = [];
        $total_volume = 0;

        foreach ($goods as $item) {
            $all_param['Weigth'] += $item['weight'];
            $total_volume += $item['volume'];
//            $all_param['Width'] += $item['width'];
//            $all_param['Height'] += $item['height'];
//            $all_param['Depth'] += $item['length'];
        }
        $all_param['Volume'] = 1;

        $pp = new PickPoint();
        $price = $pp->calculateTariff([
            'FromCity' => "Москва",
            'FromRegion' => "Московская обл.",
            'ToCity' => $pp->getCityNameByCode($city_code),
            'ToRegion' => $pp->getRegionNameByCityCode($city_code),
            'Width' => self::PICK_POINT_MAX_BOX[0],
            'Length' => self::PICK_POINT_MAX_BOX[1],
            'Depth' => $total_volume / self::PICK_POINT_MAX_BOX[0] / self::PICK_POINT_MAX_BOX[1],
            'Weight' => $all_param['Weigth'],
            'EncloseCount' => $all_param['Volume'],
        ]);

//        Yii::warning($total_volume / self::PICK_POINT_MAX_BOX[0] / self::PICK_POINT_MAX_BOX[1], 'test');

        return $price;
    }

    /**
     * Возвращает список товаров в корзине
     * @var $order_id int
     * @return array
     */
    private function getGoods($order_id = 0)
    {
        $goods = [];

        /** @var UserClientOrder $cart */
        if ($order_id > 0) {
            $cart = UserClientOrder::findOne($order_id);
        } else {
            $cart = Yii::$app->client->identity->getCart();
        }

//        Yii::warning($cart->attributes, 'test');

        $i = 0;
        /** @var UserClientOrder_Product $l */
        foreach ($cart->linkProducts as $l) {
            //К габаритам прибавляем 10%
            $length = $l->product->length + ($l->product->length * 0.1);
            $width = $l->product->width + ($l->product->width * 0.1);
            $height = $l->product->height + ($l->product->height * 0.1);

            $goods[$i] = [
                'weight' => (float)!empty($l->product->weight) ? round($l->product->weight, 3) : 0.1,
                'length' => (int)!empty($l->product->length) ? $length : 10,
                'width' => (int)!empty($l->product->width) ? $width : 10,
                'height' => (int)!empty($l->product->height) ? $height : 10
            ];
            $goods[$i]['volume'] = round($length * $width * $height, 3);
            $goods[$i]['volume_weight'] = round(($length * $width * $height) / 5000, 3); //Объемный вес
            $i++;
        }

        return $goods;
    }

    /**
     * Получает общий вес отправления.
     * @var $order_id int
     * @return float
     */
    public function getTotalWeight($order_id = 0)
    {
        $total_weight = 0; //В граммах
        $goods = $this->getGoods($order_id);

        foreach ($goods as $good) {
            $total_weight += ($good['weight'] * 1000);
        }

        $total_weight = round((float)$total_weight / 1000, 3);
//        Yii::warning('Total weight: ' . $total_weight, 'test');
        return $total_weight;
    }

    /**
     * Возвращает список тарифов люч - это код тарифа у IML, значение - наименование тарифа
     * @param int $type Тип доставки, курьер или самовывоз
     * @return mixed
     */
    public function getImlTariffList($type = null)
    {
        $iml = new Iml();
//        Yii::warning($iml->getJobs()['courier'], 'test');

        if ($type === self::IML_SD_DELIVERY_TYPE) {
            return $iml->getJobs()['courier'];
        } elseif ($type === self::IML_SS_DELIVERY_TYPE) {
            return $iml->getJobs()['pvz'];
        } elseif ($type === 'reduced_list') {
            $tariffs = [
                '24' => 'Доставка предоплаченого заказа',
                'С24' => 'Доставка на ПВЗ предоплаченного заказа',
//                'ПОЧТА 1 КЛАСС' => 'Почта РФ 1-й класс',
//                'ПОЧТА РАЗНОЕ' => 'Почта РФ разное',
            ];

            return $tariffs;
        } else {
            return $iml->getJobs(false);
        }
    }

    /**
     * Возвращает список тарифов. Ключ - это код тарифа у СДЭК, значение - наименование тарифа
     * https://confluence.cdek.ru/pages/viewpage.action?pageId=29923926 (new edition: https://api-docs.cdek.ru/29923926.html)
     * @param int $type Тип доставки
     * @return array
     */
    public function getCdekTariffList($type = self::CDEK_SD_DELIVERY_TYPE)
    {
        if ($type === self::CDEK_SD_DELIVERY_TYPE) {
            //Тарфиы склад-дверь
            return [
                233 => 'Экономичная посылка',
                137 => 'Посылка',
                //11 => 'Эксперсс-лайт',
                482 => 'Экспресс',
                122 => 'Магистральный экспресс',
                //119 => 'Экономичный экспресс',
                //16 => 'Экспресс тяжеловесы',
                125 => 'Магистральный супер экспресс',
            ];
        } elseif ($type === self::CDEK_SS_DELIVERY_TYPE) {
            return [
                234 => 'Экономичная посылка',
                136 => 'Посылка',
                //10 => 'Эксперсс-лайт',
                483 => 'Экспресс',
                62 => 'Магистральный экспресс',
                //5 => 'Экономичный экспресс',
                //15 => 'Экспресс тяжеловесы',
                63 => 'Магистральный супер экспресс',
            ];
        }

        return [];
    }

    /**
     * Возвращает список ПВЗ СДЭК для города
     * @param $city_code
     * @return array
     */
    public function getCdekPvz($city_code)
    {
        $total_result = array();
        if ((int)$city_code) {
            $sdekPvz = SdekPvz::find()->where(['sdek_id' => (int)$city_code])->all();

            if (is_array($sdekPvz)) {
                foreach ($sdekPvz as $item) {
                    $total_result[] = unserialize($item->xml);
                }

                return [
                    'status' => true,
                    'data' => $total_result,
                ];
            }
        }

        return [
            'status' => false,
            'msg' => 'Данные не получены'
        ];

//        $url = "http://integration.cdek.ru/pvzlist/v1/xml?cityid={$city_code}";
//
//        try {
//            $xml = file_get_contents($url);
//        } catch (\Exception $e) {
//            Yii::error($e->getMessage(), '_error');
//            return [
//                'status' => false,
//                'msg' => $e->getMessage(),
//            ];
//        }
//        $xml = simplexml_load_string($xml);
//        $json = json_encode($xml);
//        $result = json_decode($json, true);
//
//        if (isset($result['Pvz']['@attributes'])) {
//            //если вернулся только один ПВЗ
//            $total_result[] = $result['Pvz'];
//        } else {
//            $total_result = $result['Pvz'];
//        }
//
//        if ($result) {
//            return [
//                'status' => true,
//                'data' => $total_result,
//            ];
//        } else {
//            return [
//                'status' => false,
//                'msg' => 'Данные не получены'
//            ];
//        }
    }

    /**
     * Возвращает список ПВЗ IML для города
     * @param $city_code
     * @return array
     */
    public function getImlPvz($city_code)
    {
        $iml = new Iml();
        $result = $iml->getPvzForRegion($city_code);

//        Yii::info($result, 'test');
        return $result;
    }

    /**
     * Возвращает список постаматов PickPoint для города
     * @param int $city_code
     * @return array
     */
    public function getPickPointPvz($city_code)
    {
        $pp = new PickPoint();
        $result = $pp->getPostamatListForCity($city_code);

        return [
            'status' => true,
            'data' => $result
        ];
    }

    /**
     * Получает список служб доставки для искомого города
     * @param $term
     * @return array
     */
    public function getDsByTerm($term)
    {
        $ds_list = [];
        if ($this->getCityByTerm($term)['status']) {
            array_push($ds_list, self::DELIVERY_CDEK);
        }
        if ($this->getImlCityByTerm($term)['status']) {
            array_push($ds_list, self::DELIVERY_IML);
        }
//        if ($this->getPpCityByTerm($term)['status']) {
//            array_push($ds_list, self::DELIVERY_PICK_POINT);
//        }
        return $ds_list;
    }

    /**
     * Получает код города по названию города
     * @param $city_name
     * @return array
     */
    public function getCdekCityCodeByCityName($city_name)
    {
        //$city = $this->getCdekCityByTerm($city_name);
        $city = $this->getCityByTerm($city_name, true);
//        Yii::warning($city, 'test');

        if (!$city['status']) {
            return $city;
        } else {
            return [
                'status' => true,
                'data' => $city['data'][0]['id']
            ];
        }
    }

    /**
     * Получает код города по наименованию
     * @param $city_name
     * @return array
     */
    public function getPpCityCodeByCityName($city_name)
    {
        $city = $this->getPpCityByTerm($city_name);
//        Yii::warning($city, 'test');
        if (!$city['status']) {
            return $city;
        } else {
            return [
                'status' => true,
                'data' => array_keys($city['data'])[0]
            ];
        }

    }

    public function getImlCityCodeByCityName($city_name)
    {
        $city = $this->getImlCityByTerm($city_name);
//        Yii::warning($city, 'test');

        if (!$city['status']) {
            return $city;
        }
        if ($city['status'] && $city['data']) {
            return [
                'status' => true,
                'data' => $city['data'][0]['id']
            ];
        } else {
            return [
                'status' => false,
                'msg' => 'Данные не получены',
            ];
        }
    }

    /**
     * @param string $city_name Название города
     * @param null $cdek_city_code
     * @return array
     * [
     *  'city_codes' => [
     *      'cdek' => [
     *          'status' => true/false,
     *          'data' => код города
     *      ],
     *      'iml' => [...]
     *      'pick_point' => [...]
     *  ]
     *  'pvz_count' => [
     *      'cdek' => 3,
     *      'iml' => 2,
     *      'pick_point' => 1
     *  ]
     *  'btn_info' => [
     *      'pvz_delivery' => [
     *          'delivery_period_min' => <(int) Мин. кол-во дней доставки>,
     *          'delivery_period_max' => <(int) Макс. кол-во дней доставки>,
     *          'delivery_price' => <Цена доставки>,
     *      ]
     *      'courier_delivery' => [
     *          'delivery_period_min' => <(int) Мин. кол-во дней доставки>,
     *          'delivery_period_max' => <(int) Макс. кол-во дней доставки>,
     *          'delivery_price' => <Цена доставки>,
     *      ]
     *  ]
     *  'cdek' => [
     *          [
     *              'status' => <(bool) Статус возможна или нет доставка>,
     *              'tariff_id' => <(int) Идентификатор тарифа>,
     *              'tariff_name' => <(string) Название тарифа>,
     *              'delivery_date' => <(d.m.Y) Дата доставки>,
     *              'delivery_period_min' => <(int) Мин. кол-во дней доставки>,
     *              'delivery_period_max' => <(int) Макс. кол-во дней доставки>,
     *              'delivery_price' => <Цена доставки>
     *          ]
     *   ]
     *  'iml' => [
     *          [
     *              'status' => <(bool) Статус возможна или нет доставка>,
     *              'tariff_id' => <(int) Идентификатор тарифа>,
     *              'tariff_name' => <(string) Название тарифа>,
     *              'delivery_date' => <(d.m.Y) Дата доставки>,
     *              'delivery_period_min' => <(int) Мин. кол-во дней доставки>,
     *              'delivery_period_max' => <(int) Макс. кол-во дней доставки>,
     *              'delivery_price' => <Цена доставки>
     *          ]
     *   ]
     *
     * ]
     */
    public function getDeliveriesInfo($city_name, $cdek_city_code = null)
    {
        $total_result = [];
//        $counter = 0;

        if ($cdek_city_code) {
            $cdek_city_id = [
                'status' => true,
                'data' => $cdek_city_code
            ];
        } else {
            $cdek_city_id = $this->getCdekCityCodeByCityName($city_name);
            //echo "<script>console.log('city_name: {$city_name}')</script>";
            //echo "<script>console.log('cdek_city_id: {$cdek_city_id}')</script>";
        }

        if ($cdek_city_id['status']) {
            $info = $this->getCdekSum($cdek_city_id['data']);
            $cdek_tarrifs = $this->getCdekTariffList(self::CDEK_SD_DELIVERY_TYPE) +
                $this->getCdekTariffList(self::CDEK_SS_DELIVERY_TYPE);

            if ($info['status'] && $info['data']) {
                $cdek = &$total_result[self::DELIVERY_CDEK];
                foreach ($info['data'] as $tariff) {
                    $cdek[] = [
                        'status' => $tariff['status'],
                        'tariff_id' => $tariff['tariffId'],
                        'tariff_name' => $cdek_tarrifs[$tariff['tariffId']],
                        'delivery_date' => $this->getDateForPeriod($tariff['result']['deliveryPeriodMax']),
                        'delivery_period_min' => (int)$tariff['result']['deliveryPeriodMin'],
                        'delivery_period_max' => (int)$tariff['result']['deliveryPeriodMax'] + 1,
                        'delivery_price' => ceil($tariff['result']['price']),
                    ];
                }
            }
        } else {
            $total_result[self::DELIVERY_CDEK] = [];
        }

        $iml_city_code = $this->getImlCityCodeByCityName($city_name);
        if ($iml_city_code['status']) {
            $tariffs = $this->getImlTariffList('reduced_list');
            foreach ($tariffs as $tariff_code => $tariff_description) {
                $tariff = $this->getImlSum($iml_city_code['data'], $tariff_code);
                //Ответ сервера IML
                $total_result['response'][self::DELIVERY_IML][$tariff_code] = $tariff['response'];

                $iml = &$total_result[self::DELIVERY_IML];
                if ($tariff['status']) {
                    $tariff = $tariff['data'];
                    $max_period = ceil((strtotime($tariff['DeliveryDate']) - time()) / (60 * 60 * 24)) + 1;
                    $iml[$tariff_code] = [
                        'status' => true,
                        'tariff_id' => $tariff['Request']['Job'],
                        'tariff_name' => $tariff_description,
                        'delivery_date' => date('d.m.Y', strtotime($tariff['DeliveryDate'])),
                        'delivery_period_min' => null,
                        'delivery_period_max' => $max_period,
                        'delivery_price' => ceil($tariff['Price']),
                    ];
                    if ($tariff['PriceList']) {
                        foreach ($tariff['PriceList'] as $price) {
                            $price['DeliveryDate'] = date('d.m.Y', strtotime($price['DeliveryDate']));
                            $price['Price'] = ceil($price['Price']);
                            $iml[$tariff_code]['price_list'][] = $price;
                        }
                    } else {
                        $tariff['PriceList'] = [];
                    }
                } else {
                    $iml[$tariff_code] = [];
                }
            }
        } else {
            $total_result[self::DELIVERY_IML] = [];
            $total_result['response'][self::DELIVERY_IML] = [];
        }

        $pp_city_code = $this->getPpCityCodeByCityName($city_name);
        if ($pp_city_code['status'] && $pp_city_code['data']) {
            $info = $this->getPpSum($pp_city_code['data']);
            $total_result['response'][self::DELIVERY_PICK_POINT] = $info['response'];
            $can_contain = $this->canContain(self::DELIVERY_PICK_POINT);
            if ($info['status']) {
                foreach ($info['data']['Services'] as $tariff) {
                    $pp = &$total_result[self::DELIVERY_PICK_POINT];
                    $pp[] = [
                        'status' => true,
                        'tariff_id' => $tariff['DeliveryMode'],
                        'tariff_name' => $tariff['Name'],
                        'delivery_date' => $tariff['DeliveryDate'],
                        'delivery_period_min' => (int)$info['data']['DPMin'],
                        'delivery_period_max' => (int)$info['data']['DPMax'] + 1,
                        'delivery_price' => ceil($tariff['Tariff']),
                        'can_contain' => $can_contain,
                    ];
                }
            } else {
                $total_result[self::DELIVERY_PICK_POINT] = [];
            }
        } else {
            $total_result[self::DELIVERY_PICK_POINT] = [];
        }

        //Получаем самые дешевые тарифы по СДЭК (для вывода на кнопках Самвывоз и Доставка)
        $total_result['btn_info'] = $this->getMinTariff($total_result['cdek']);

        $city_codes = [
            self::DELIVERY_CDEK => $cdek_city_id,
            self::DELIVERY_IML => $iml_city_code,
            self::DELIVERY_PICK_POINT => $pp_city_code,
        ];

        $total_result['pvz_count'] = $this->getPvzCount($city_codes);

        $total_result['city_codes'] = $city_codes;

        Yii::warning($total_result, 'test');

        return $total_result;
    }

    /**
     * Получает дату которая получится кода пройдет указанное кол-во дней
     * @param int $period Кол-во дней
     * @param string $format
     * @return false|string
     */
    private function getDateForPeriod($period, $format = 'd.m.Y')
    {
        $period = (int)$period;

        return date($format, time() + ($period * (60 * 60 * 24)));
    }

    /**
     * Список ПВЗ СДЭК для города
     * @param $city_name
     * @return array
     */
    public function getCdekPvzByCityName($city_name)
    {
        $city_name = $this->getCdekCityCodeByCityName($city_name);

        if ($city_name['status']) {
            return $this->getCdekPvz($city_name['data']);
        } else {
            return $city_name;
        }
    }

    /**
     * Список ПВЗ IML для города
     * @param $city_name
     * @return array
     */
    public function getImlPvzByCityName($city_name)
    {
        $result = $this->getImlCityCodeByCityName($city_name);
        if ($result['status']) {
            return $this->getImlPvz($result['data']);
        }

        return $result;
    }

    /**
     * Список ПВЗ PickPoint для города
     * @param $city_name
     * @return array
     */
    public function getPickPointPvzByCityName($city_name)
    {
        return $this->getPickPointPvz($this->getPpCityCodeByCityName($city_name));
    }

    /**
     * @param array $data
     * @return array
     *  {
     *      'pvz_delivery' => {
     *          'delivery_period_min' => (int) Мин. кол-во дней доставки,
     *          'delivery_period_max' => (int) Макс. кол-во дней доставки,
     *          'delivery_price' => Цена доставки,
     *      }
     *      'courier_delivery' => {
     *          'delivery_period_min' => (int) Мин. кол-во дней доставки,
     *          'delivery_period_max' => (int) Макс. кол-во дней доставки,
     *          'delivery_price' => Цена доставки,
     *      }
     *  }
     */
    public function getMinTariff($data)
    {
        $pvz_tariffs = array_keys($this->getCdekTariffList(self::CDEK_SS_DELIVERY_TYPE));
//        $courier_tariffs = array_keys($this->getCdekTariffList(self::CDEK_SD_DELIVERY_TYPE));

        $min_price_pvz = 999999999;
        $tariff_id_pvz = 0;

        $min_price_courier = 999999999;
        $tariff_id_courier = 0;

        for ($i = 0; $i < count($data); $i++) {
            $price = $data[$i]['delivery_price'];
            if ($data[$i]['status'] == true) {
                if (in_array($data[$i]['tariff_id'], $pvz_tariffs)) {
                    if ($min_price_pvz > $price) {
                        $min_price_pvz = $price;
                        $tariff_id_pvz = $i;
                    }
                } else {
                    if ($min_price_courier > $price) {
                        $min_price_courier = $price;
                        $tariff_id_courier = $i;
                    }
                }
            }
        }

        $pvz_tariff = $data[$tariff_id_pvz];
        $courier_tariff = $data[$tariff_id_courier];

        return [
            'pvz_delivery' => [
                'delivery_period_min' => $pvz_tariff['delivery_period_min'],
                'delivery_period_max' => $pvz_tariff['delivery_period_max'],
                'delivery_price' => $pvz_tariff['delivery_price'],
            ],
            'courier_delivery' => [
                'delivery_period_min' => $courier_tariff['delivery_period_min'],
                'delivery_period_max' => $courier_tariff['delivery_period_max'],
                'delivery_price' => $courier_tariff['delivery_price'],
            ]
        ];
    }

    /**
     * @param array $city_codes
     * @return array
     */
    public function getPvzCount($city_codes)
    {
        $cdek_count = 0;
        $pp_count = 0;
        $iml_count = 0;

        if ($city_codes[self::DELIVERY_CDEK]) {
            if (isset($city_codes[self::DELIVERY_CDEK]['status']) && $city_codes[self::DELIVERY_CDEK]['status']) {
                $cdek_pvz = $this->getCdekPvz($city_codes[self::DELIVERY_CDEK]['data']);
                if ($cdek_pvz['status']) {
                    $cdek_count = count($cdek_pvz['data']);
                }
            } else {
                $cdek_count = 0;
            }
        }


        if ($city_codes[self::DELIVERY_PICK_POINT]) {
            if (isset($city_codes[self::DELIVERY_PICK_POINT]['status']) && $city_codes[self::DELIVERY_PICK_POINT]['status']) {
                $pp_pvz = $this->getPickPointPvz($city_codes[self::DELIVERY_PICK_POINT]['data']);
                if ($pp_pvz['status']) {
                    $pp_count = count($pp_pvz['data']);
                }
            } else {
                $pp_count = 0;
            }
        }


        if ($city_codes[self::DELIVERY_IML]) {
            if (isset($city_codes[self::DELIVERY_IML]['status']) && $city_codes[self::DELIVERY_IML]['status']) {
                $iml_pvz = $this->getImlPvz($city_codes[self::DELIVERY_IML]['data']);
                $iml_count = count($iml_pvz);
            } else {
                $iml_count = 0;
            }
        }


        return [
            self::DELIVERY_CDEK => $cdek_count,
            self::DELIVERY_PICK_POINT => $pp_count,
            self::DELIVERY_IML => $iml_count,
        ];
    }

    public function getDeliveryServicesName()
    {
        return [
            self::DELIVERY_CDEK => 'СДЭК',
            self::DELIVERY_PICK_POINT => 'Pick Point',
            self::DELIVERY_IML => 'IML',
            self::PICKUP_FROM_BRATISLAVSKAYA => 'Самовывоз (Братиславская)',
            self::DELIVERY_COURIER => 'Курьер',
        ];
    }

    /**
     * Получает тарифы доставки курьером
     * @return mixed
     */
    public function getImlCourierTariff()
    {
        $iml = new Iml();
        return $iml->getJobs()['courier'];
    }

    /**
     * Получает тарифы доставки в ПВЗ
     * @return mixed
     */
    public function getImlPvzTariff()
    {
        $iml = new Iml();
        return $iml->getJobs()['pvz'];
    }

    /**
     * Определяет влезет ли заказанное в максимально возможные размеры ячейки
     * @param string $dc Компания - доставщик
     * @return bool
     */
    private function canContain($dc)
    {
        $box = [];

        if ($dc === self::DELIVERY_PICK_POINT) {
            $box = self::PICK_POINT_MAX_BOX;
        }

        if (count($box) > 0) {
            $items_volume = 0;
            foreach ($this->getGoods() as $product) {
                $items_volume += $product['volume'];
            }
            $total_width = $box[0];
            $total_length = $box[1];
            $total_depth = round($items_volume / $total_width / $total_length, 3);
//            Yii::warning('Глубина товаров: ' . $total_depth, 'test');
//            Yii::warning('Макс. глубина коробки: ' . $box[2], 'test');
            if ($total_depth <= $box[2]) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    protected function existIsOversized()
    {
        foreach ($this->getGoods() as $product) {
            if ($product['is_oversized']) {
                return true;
            }
        }

        return false;
    }

    public function getCourierClientPrice($timeInterval, $order_id = 0)
    {
        $price = $this->getCourierPrice($order_id);
        if ($order_id) {
            $order = UserClientOrder::findOne($order_id);
        } else {
            $order = Yii::$app->client->identity->getCart();
        }

        if ($order) {
            // Крупногабаритные товары
            foreach ($order->linkProducts as $product) {
                if ($product->product->is_oversized) {
                    return $price;
                }
            }

            if ('tomorrow' == strstr($timeInterval, '_', true)) {
                $discount = $order->user->profile->sale_ms_id;
                $sum = $order->getSum();
                if (!empty($discount)) {
                    switch ($discount) {
                        case 'Скидка 1':
                        case 'Скидка 2':
                            if ($sum >= (int)Yii::$app->keyStorageApp->get('courier_free_sum_group_1') * 100) {
                                $price = 0;
                            }
                            break;
                        case 'Скидка 3':
                            if ($sum >= (int)Yii::$app->keyStorageApp->get('courier_free_sum_group_2') * 100) {
                                $price = 0;
                            }
                            break;
                        case 'Скидка 4':
                        case 'Скидка 5':
                        case 'Скидка 6':
                            if ($sum >= (int)Yii::$app->keyStorageApp->get('courier_free_sum_group_3') * 100) {
                                $price = 0;
                            }
                            break;
                    }
                } else {
                    if ($sum >= (int)Yii::$app->keyStorageApp->get('courier_free_sum_group_0') * 100) {
                        $price = 0;
                    }
                }
            } elseif ('today' == strstr($timeInterval, '_', true)) {
                $price += (int)Yii::$app->keyStorageApp->get('courier_additional_price') * 100;
            }
        }

        return $price;
    }

    private function getCourierPrice($order_id = 0)
    {
        $price = 0;
        $weight = $this->getTotalWeight($order_id);
        if ($weight < (int)Yii::$app->keyStorageApp->get('courier_weight_1')) {
            $price = (int)Yii::$app->keyStorageApp->get('courier_price_by_weight_1') * 100;
        } elseif ($weight >= (int)Yii::$app->keyStorageApp->get('courier_weight_1') && $weight < (int)Yii::$app->keyStorageApp->get('courier_weight_2')) {
            $price = (int)Yii::$app->keyStorageApp->get('courier_price_by_weight_2') * 100;
        } elseif ($weight >= (int)Yii::$app->keyStorageApp->get('courier_weight_2')) {
            $price = (int)Yii::$app->keyStorageApp->get('courier_price_by_weight_3') * 100;
        }

        return $price;
    }
}