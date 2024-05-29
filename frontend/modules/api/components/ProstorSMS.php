<?php

namespace frontend\modules\api\components;

use yii\base\Component;

use GuzzleHttp\Client;

/**
 * @link https://prostor-sms.ru/smsapi/rest_messages_api.prostor-sms.ru.pdf
 * @link https://prostor-sms.ru/smsapi/json_messages_api.prostor-sms.ru.pdf
 */
class ProstorSMS extends Component
{
    /* @var Client $client */
    private $client;

    public $username;

    public $password;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $apiUrl = "https://{$config['username']}:{$config['password']}@api.prostor-sms.ru/messages/v2/";

        $this->client = new Client([
            'base_uri' => $apiUrl,
            'timeout'  => 5.0,
        ]);
    }

    private function _clearPhone($phone)
    {
        return preg_replace('/[() -]+/', '', $phone);
    }

    private function _sendGetRequest($query, $params)
    {
        $fullQuery = $query .'?'. http_build_query($params);
        $response = $this->client->get($fullQuery);
        $logMessage = 'ProstorSMS/'. $query .':'. $response->getBody();
        if ($response->getStatusCode() != 200) {
            \Yii::error($logMessage, 'sms');
            return false;
        } else {
            \Yii::info($logMessage, 'sms');
            return true;
        }
    }

    public function send($phone, $text)
    {
        return $this->_sendGetRequest('send', [
            'phone' => $this->_clearPhone($phone),
            'text' => $text
        ]);
    }

    public function status($phone)
    {
        return $this->_sendGetRequest('status', [
            'id' => $this->_clearPhone($phone)
        ]);
    }
}
