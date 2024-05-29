<?php

namespace common\components\subdomains;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class Subdomains
 * @package common\components\subdomains
 */
class Subdomains extends Component
{
    /**
     * @var string
     */
    public $modelClass = '\common\models\Subdomains';

    /**
     * @param $subdomain
     * @return mixed
     */
    protected function getModel($subdomain)
    {
        $query = call_user_func($this->modelClass . '::find');

        return $query->where(['subdomain' => $subdomain])->cache(300)->asArray()->one();
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        $domain = parse_url(env('FRONTEND_HOST_INFO'), PHP_URL_HOST);
        $domain = $domain ? $domain : 'tattoofeel.ru';

        $model = $this->getModel(str_replace('.' . $domain, '', $_SERVER['HTTP_HOST']));
        $keys = Yii::$app->keyStorageApp->getAll(['phone_1', 'time',  'time_showroom', 'address']);

        if ($model) {
            if (empty($model['phone'])) {
                $model['phone'] = $keys['phone_1'];
            }

            if (empty($model['work_hours_showroom'])) {
                $model['work_hours_showroom'] = $keys['time_showroom'];
            }

            if (empty($model['work_time'])) {
                $model['work_time'] = $keys['time'];
            }
        } else {
            if ($domain == $_SERVER['HTTP_HOST']) {
                $model['id'] = 0;
                $model['subdomain'] = '';
                $model['city'] = 'Москва';
                $model['word_form'] = 'Москве';
                $model['address'] = $keys['address'];
                $model['phone'] = $keys['phone_1'];
                $model['work_time'] = $keys['time'];
                $model['work_hours_showroom'] = $keys['time_showroom'];
            }
        }

        return $model;
    }
}
