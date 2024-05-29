<?php

namespace common\components\cdek;

use yii\base\Component;
use yii\base\Exception;

class CalculateTariffList extends Component
{
    public $senderCityId = 44;

    public $receiverCityId = null;

    public $dateExecute = null;

    public $tariffId = null;

    public $tariffList = null;

    public $goods = [];

    private $version = "1.0";

    private $url = 'http://api.cdek.ru/calculator/calculate_tarifflist.php';

    private $authLogin = null;

    private $authPassword = null;

    private $requestData = [];

    private $result = ['status' => true, 'data' => [], 'msg' => []];

    public function __construct($configs) {
        parent::__construct($configs);

        if (empty($this->dateExecute))
            $this->dateExecute = date('Y-m-d');

        if (empty($this->receiverCityId)) {
            $this->result['status'] = false;
            $this->result['msg'][] = 'Не задан город получателя';
        }

        if (empty($this->goods)) {
            $this->result['status'] = false;
            $this->result['msg'][] = 'Не задан спсиок мест';
        }

        if (empty($this->tariffId) && empty($this->tariffList)) {
            $this->result['status'] = false;
            $this->result['msg'][] = 'Не задан тариф';
        }

        $this->authLogin = env('API_CDEK_LOGIN');
        $this->authPassword = env('API_CDEK_PASSWORD');
        $this->setRequestData();

    }

    public function calculate() {
        if ($this->result['status']) {
            $response = $this->send();

            if ($response['result']) {
                $this->result['data'] = $response['result'];
            } else {
                $this->result['status'] = false;
                $this->result['msg'] = 'Ошибка вычислений';
                $this->result['data'] = $response;
            }
        }

        return $this->result;
    }

    private function send() {
        $data_string = json_encode($this->requestData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $result = curl_exec($ch);
        curl_close($ch);

        $r = json_decode($result, true);
        //\Yii::info($this->requestData, 'test');
        //\Yii::info($r, 'test');

        return $r;
    }

    private function setRequestData() {
        if($this->authLogin && $this->authPassword) {
            $this->requestData['authLogin'] = $this->authLogin;
            $this->requestData['secure'] = md5($this->dateExecute . '&' . $this->authPassword);
        }

        $this->requestData['version'] = $this->version;
        $this->requestData['receiverCityId'] = $this->receiverCityId;
        $this->requestData['senderCityId'] = $this->senderCityId;
        $this->requestData['dateExecute'] = $this->dateExecute;
        $this->requestData['goods'] = $this->goods;

        if (!empty($this->tariffId))
            $this->requestData['tariffId'] = $this->tariffId;
        else
            $this->requestData['tariffList'] = $this->prepareTariffList();
    }

    private function prepareTariffList() {
        $tl = [];

        foreach ($this->tariffList as $t)
            $tl[] = ['id' => $t];

        return $tl;
    }
}
