<?php

namespace common\components\pickPoint;

use backend\modules\import\models\PickPointTerminal;
use common\models\DeliverySettings;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @property string $login
 * @property string $pass
 * @property string $base_url URL API
 * @property string $base_test_url тестовый URL API
 * @property string $method АПИ метод
 * @property array $request_data данные для отправки в запросе
 * @property string $session_id Идентификатор сессии
 * @property string $ikn Номер контракта
 */
class PickPoint extends Component
{
    const SESSION_ID_KEY = 'pick_point_session_id';
    const SESSION_END_DATE_KEY = 'pick_point_session_id_end_date';

    private $login;
    private $pass;
    private $base_test_url = 'https://e-solution.pickpoint.ru/apitest/';
    private $base_url = 'https://e-solution.pickpoint.ru/api/';
    private $method = 'login';
    private $request_data;
    private $session_id;
    private $ikn;

    public function __construct()
    {
        parent::__construct();

        $this->login = env('API_PP_USER');
        $this->pass = env('API_PP_PASSWORD');
        $this->ikn = env('API_PP_IKN');

        //Поучаем идентификатор сессии
        $this->session_id = $this->getSessionId();
    }

    /**
     * Авторизация
     * @return array
     */
    private function login()
    {
        $this->method = 'login';
        $this->request_data = [
            'Login' => $this->login,
            'Password' => $this->pass,
        ];
        return $this->send(false);

    }

    /**
     * @param bool $is_test Тестовый запрос или нет
     * @return mixed
     */
    private function send($is_test = true)
    {
        if (!$is_test) {
            $url = $this->base_url;
        } else {
            $url = $this->base_test_url;
        }
//        set_time_limit(0);

        if ($this->session_id) {
            $this->request_data['SessionId'] = $this->session_id;
        }
        $this->request_data['IKN'] = $this->ikn;
        //Yii::info($this->request_data, 'test');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . $this->method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->request_data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $output = curl_exec($ch);
//        $info = curl_getinfo($ch);
        curl_close($ch);
        list($header, $body) = explode("\r\n\r\n", $output, 2);

//        Yii::info($info, 'test');
//        Yii::info($header, 'test');
//        Yii::info($body, 'test');
//        Yii::info($output, 'test');

        $result = json_decode($body, true);
        //Yii::info($result, 'test');

        if ($result['ErrorCode'] && !$result['ErrorMessage']) {
            $total_result = [
                'status' => false,
                'msg' => $result['ErrorMessage']
            ];
        } else {
            if ($result) {
                $total_result = [
                    'status' => true,
                    'data' => $result
                ];
            } else {
                $total_result = [
                    'status' => false,
                    'data' => 'Данные не получены'
                ];
            }

        }
        $total_result['response'] = $result;
        //Yii::info($total_result, 'test');

        return $total_result;
    }

    /**
     * Получает Идентифкатор сессии
     * @return string
     */
    private function getSessionId()
    {
        $settings = new DeliverySettings();

        //Проверяем записана ли сессия в базе
        $session_id = $settings->getValueByKey(self::SESSION_ID_KEY);
        $session_end_date = $settings->getValueByKey(self::SESSION_END_DATE_KEY);

//        Yii::warning('Session expired: ' . $this->isExpired($session_end_date), 'test');
//        Yii::warning('Session key: ' . $session_id, 'test');

        if ($this->isExpired($session_end_date)) {
            //Запрашиваем новую сессию
            $result = $this->login();
            if (!$result['status'] || $result['data']['ErrorCode'] != 0) {
                Yii::error($result['data']['ErrorMessage'], '_error');
                return false;
            }
            if ($this->saveNewSession($result['data'])) {
                $session_id = $result['SessionId'];
            } else {
                return null;
            }
        }
        return $session_id;
    }

    /**
     * Сохраняет сессию в базу
     * @param array $data Идентификатор сессии
     * @return bool
     */
    private function saveNewSession($data)
    {
        $model = DeliverySettings::findOne(['key' => self::SESSION_ID_KEY]);
        $model->value = $data['SessionId'];
        if (!$model->save()) {
            Yii::error($model->errors, '_error');
            return false;
        }

        $date_model = DeliverySettings::findOne(['key' => self::SESSION_END_DATE_KEY]);
        $date_model->value = $data['ExpiresIn'];
        if (!$date_model->save()) {
            Yii::error($date_model->errors, '_error');
            return false;
        }

        return true;
    }

    /**
     * Проверяет меньше текущей или нет переданная дата
     * @param string $date строка с датой
     * @return bool
     */
    private function isExpired($date)
    {
        return (bool)(strtotime($date) < time());
    }

    /**
     * Получает список статусов
     * @return array
     */
    public function getStatuses()
    {
        $this->method = 'getstates';
        $result = $this->getList();
        return $result;
    }

    /**
     * Получает список городов
     * @return array
     */
    public function getCities()
    {
//        $this->method = 'citylist';
//        $result = $this->getList();
//        return $result;

        $table_name = PickPointTerminal::tableName();

        $city_ids = PickPointTerminal::find()->select(['id'])->groupBy(['city_id', 'region'])->column();

        if (empty($city_ids) || !count($city_ids)) {
            return [];
        }
        $sql2 = "SELECT CONCAT(city_name, ', ', region) AS address, city_id FROM " . $table_name
            . " WHERE id IN (" . implode(',', $city_ids) . ")";
        $addresses = PickPointTerminal::findBySql($sql2)->all();

        $result = ArrayHelper::map($addresses, 'city_id', 'address');

//        Yii::warning($result, 'test');

        return $result;

    }

    /**
     * Получает список чего-либо
     * @return mixed
     */
    public function getList()
    {
        return json_decode(file_get_contents($this->base_url . $this->method), true);
    }

    /**
     * Получает список постаматов для договора
     * Метод необходимо добавить в CRON
     */
    public function getPostamatList()
    {
        $this->method = 'clientpostamatlist';

        $result = $this->send(false);

//        Yii::warning($result, 'test');

        return $result;

    }

    /**
     * @param $city_code
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getPostamatListForCity($city_code)
    {
        $postamats = PickPointTerminal::find()->andWhere(['city_id' => $city_code])->asArray()->all();
//        Yii::warning($postamats, 'test');

        return $postamats;
    }

    /**
     * Расчет стоимости доставки
     * @param $data
     * Структура  $data:
     * {
     * “InvoiceNumber”: <Номер отправления, не обязательное поле>,
     * “FromCity”:            <Город сдачи отправления>,
     * “FromRegion”:      <Регион города сдачи отправления>,
     * “ToCity“:                <Город назначения>,
     * “ToRegion“:          <Регион назначения>,
     * “PTNumber”:         <Пункт выдачи (назначения) отправления>,
     * “GettingType”:     <Вид приема, не обязательное поле >,
     * “EncloseCount”:  <Количество мест, по умолчанию одно, не обязательное поле>,
     * “Length”:                              <Длина отправления, см>,
     * “Depth”:                <Глубина отправления, см>,
     * “Width”:                 <Ширина отправления, см>,
     * “Weight”:                              <Вес отправления, не обязательное поле, по умолчанию 1кг>
     * }
     *
     * @return array
     */
    public function calculateTariff($data)
    {
        $this->request_data = $data;
        $this->method = 'calctariff';

        $result = $this->send(false);

//        Yii::warning($result, 'test');

        return $result;
    }

    /**
     * @param $city_name
     * @return array
     */
    public function getCitiesByTerm($city_name)
    {
        $table_name = PickPointTerminal::tableName();

        if ($city_name) {
            $city_ids = PickPointTerminal::find()->select(['id'])->andWhere([
                'LIKE',
                'city_name',
                $city_name
            ])->column();
        }

        if (isset($city_ids)) {
            $sql2 = "SELECT CONCAT(city_name, ', ', region) AS address, city_id FROM " . $table_name
                . " WHERE id IN (" . implode(',', $city_ids) . ")";
            try {
                $addresses = PickPointTerminal::findBySql($sql2)->all();
            } catch (\Exception $e) {
                Yii::error($e->getMessage(), '_error');
                return [
                    'status' => false,
                    'msg' => $e->getMessage(),
                ];
            }
        } else {
            return [
                'status' => false,
                'msg' => 'Cities for PickPoint services not found',
            ];
        }

        $result['status'] = true;
        $result['data'] = ArrayHelper::map($addresses, 'city_id', 'address');

//        Yii::warning($result, 'test');

        return $result;
    }

    /**
     * @param $code
     * @return string
     */
    public function getCityNameByCode($code)
    {
        /** @var PickPointTerminal $terminal */
        $terminal = PickPointTerminal::find()->andWhere(['city_id' => $code])->one();
        return $terminal->city_name;
    }

    /**
     * @param $code
     * @return string
     */
    public function getRegionNameByCityCode($code)
    {
        /** @var PickPointTerminal $terminal */
        $terminal = PickPointTerminal::find()->andWhere(['city_id' => $code])->one();
        return $terminal->region;
    }


}