<?php

namespace common\components\iml;

use backend\modules\import\models\ImlCity;
use common\models\DeliveryCity;
use Yii;
use yii\base\Component;

/**
 * @property  string $login Логин IML
 * @property  string $pass Пароль IML
 * @property  string $url
 * @property  string $get_price_url Url для расчета доставки
 * @property  array $request_data Данные для запроса
 *
 */
class Iml extends Component
{
    private $login;
    private $pass;
    private $url;
    private $get_price_url = 'http://api.iml.ru/v5/GetPrice';
    private $request_data;

    private $result = ['status' => true, 'data' => [], 'msg' => []];

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->login = env('API_IML_USER');
        $this->pass = env('API_IML_PASSWORD');
    }

    /**
     * получает список Услуг
     *
     * @param bool $break
     * @return mixed
     */
    public function getJobs($break = true)
    {
//        $result = json_decode(file_get_contents('http://list.iml.ru/service'), true);
//        Yii::warning($result, 'test');
//        return $result;

        if ($break) {
            return [
                'courier' => [
                    '24' => 'Доставка предоплаченого заказа',
                    '24КО' => 'Доставка с наличным расчетом',
                ],
                'pvz' => [
                    'С24' => 'Доставка на ПВЗ предоплаченного заказа',
                    'С24НАЛ' => 'Доставка на ПВЗ с наличным расчетом',
                ],
                'post' => [
                    'ПОЧТА 1 КЛАСС' => 'Почта РФ 1-й класс',
                    'ПОЧТА РАЗНОЕ' => 'Почта РФ разное',
                ]
            ];
        } else {
            return [
                '24' => 'Доставка предоплаченого заказа',
                '24КО' => 'Доставка с наличным расчетом',
                'С24' => 'Доставка на ПВЗ предоплаченного заказа',
                'С24НАЛ' => 'Доставка на ПВЗ с наличным расчетом',
            ];
        }

    }

    /**
     * получает список Регионов
     *
     * @return mixed
     */
    public function getRegions()
    {
        $result = json_decode(file_get_contents('http://list.iml.ru/region'), true);
//        Yii::info($result, 'test');

        return $result;
    }

    /**
     * получает список Статусов
     *
     * @return mixed
     */
    public function getStatuses()
    {
        $result = json_decode(file_get_contents('http://list.iml.ru/status'), true);
        //Yii::info($result, 'test');

        return $result;
    }

    /**
     * Получает список пунктов выдачи для региона (По факту Регионы - это города)
     * @param string $region Код региона
     * @return mixed
     */
    public function getPvzForRegion($region = 'МОСКВА')
    {
        $result = json_decode(file_get_contents('http://list.iml.ru/sd?type=json&RegionCode=' . urlencode($region)), true);

        return $result;
    }

    public function getPvzByJob($job)
    {
        $result = json_decode(file_get_contents('http://list.iml.ru/LocationResource?job=' . $job), true);

        return $result;
    }

    public function getRegionCityList()
    {
        $result = json_decode(file_get_contents('http://list.iml.ru/RegionCity'), true);
//        Yii::info($result, 'test');

        return $result;
    }

    /**
     * @param string $term Набор символов для поиска
     * @return array
     */
    public function getRegionsByTerm_no_use($term)
    {
        if (mb_strlen($term, 'utf-8') >= 3) {
            $regions = $this->getRegionCityList();
            $result_search = [];
            foreach ($regions as $region_city) {
                $reg_base = mb_strtolower($region_city['City']);
                $reg_term = mb_strtolower($term);
                $search_result = strpos($reg_base, $reg_term);
                if ($search_result !== false) {
//                    Yii::info($reg_base . ' - ' . $reg_term, 'test');
                    array_push($result_search, $region_city);
                }
            }
//            Yii::info($result_search, 'test');

            return $result_search;
        } else {
            //Yii::info(mb_strlen($term, 'utf-8'), 'test');
            return [];
        }
    }

    /**
     * Получает список городов по строке
     * @param string $term Искомое сочетание букв
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCitiesByTerm($term)
    {
        $fiasCities = DeliveryCity::find()
            ->where(['like', 'city_full', $term.'%', false])
            ->andWhere(['not', ['fias_id' => null]])
            ->orderBy(['city' => SORT_ASC])
            ->limit(10)
            ->all();
        $fiasCity = 'undefined';
        if ($fiasCities) {
            $fiasCity = $fiasCities[0]->fias_id;
        }
//        print_r($fiasCities);
//        die($fiasCity);
        $cities = ImlCity::find()->andWhere(['fias' => $fiasCity])->asArray()->all();

//        Yii::warning($cities, 'test');

        if (!$cities) {
            return [
                'status' => false,
                'msg' => 'Cities for IML service not found',
            ];
        }

        return [
            'status' => true,
            'data' => $cities,
        ];

    }

    /**
     * @return array
     */
    public function calculatePrice()
    {
        $this->url = $this->get_price_url;
        if (!$this->request_data) {
            $this->result['status'] = false;
            $this->result['msg'] = 'Нет данных для запроса';
        } else {
            $this->result = $this->send();
        }
//        Yii::warning($this->request_data, 'test');
//        Yii::warning($this->result, 'test');

        return $this->result;
    }

    private function send()
    {
        //логин и пароль, подходят от личного кабинета
        $login = $this->login;
        $pass = $this->pass;

        $content = $this->request_data;
        //Yii::info($content, 'test');

        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //для получения ответа в формате XML раскомментируйте строку ниже
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept:application/xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($content));
        curl_setopt($curl, CURLOPT_USERPWD, $login . ":" . $pass);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $result = json_decode($response, true); // результат запроса
        curl_close($curl);

        //Yii::info($response, 'test');
        //Yii::info($result, 'test');

        if ($result['Errors'] == null) {
            $total_result ['status'] = true;
            $total_result ['data'] = $result;
        } else {
            $total_result ['status'] = false;
            $total_result ['msg'] = $result['Errors'][0]['ErrorMessage'];
        }
        $total_result['response'] = $result;
        return $total_result;
    }

    /**
     * $data должен содержать поля для расчета тарифа:
     * Job (string): Job – услуга, Code из справочника услуг, находится по адресу http://list.iml.ru/service. , (обязательное)
     * RegionFrom (string, optional): Регион отправления. Code из таблицы регионов, находится
     *  по адресу http://list.iml.ru/region (обязательное если указано indexFrom),
     * RegionTo (string, optional): Регион получения. Code из таблицы регионов, находится
     *  по адресу http://list.iml.ru/region , (обязательное если указано indexTo)
     * Weigth (number, optional): вес(кг) Дробное число, указывается с разделетилем целой и дробной части точка. ,(обязательное)
     * Volume (integer, optional): Кол-во мест в заказе, от 1 до 9 ,(обязательное)
     * SpecialCode (integer, optional): Код пункта самовывоза, только для самовывозных услуг,
     *  параметр RequestCode в соответствующем справочнике, находится по адресу http://list.iml.ru/sd ,
     * ReceiptAmount (number, optional): Наложенный платеж ,
     * DeclaredValue (number, optional): Оценочная стоимость ,
     * Width (number, optional): Ширина ,
     * Height (number, optional): Высота ,
     * Depth (number, optional): Глубина ,
     * ReceiveDate (string, optional): Дата получения товара на склад IML Формат даты поддерживаемые
     *  системой "YYYY-MM-DDThh:mm", ,
     * IndexFrom (string, optional): Почтовый индекс отправления. Code из таблицы регионов, находится
     *  по адресу http://list.iml.ru/postcode , (обязательное если указано regionFrom)
     * IndexTo (string, optional): Почтовый индекс получения. Code из таблицы регионов, находится
     *  по адресу http://list.iml.ru/postcode ,(обязательное если указано regionTo)
     * DeliveryAddress (string, optional): Адрес назначения отправления
     *
     * @param array $data
     */
    public function setRequestData($data)
    {
        $this->request_data = $data;
//        Yii::info($this->request_data, 'test');
    }

    public function getResourceLimit()
    {
        $result = json_decode(file_get_contents('http://list.iml.ru/ResourceLimit'), true);
        Yii::warning($result, 'test');

        return $result;
    }

}
