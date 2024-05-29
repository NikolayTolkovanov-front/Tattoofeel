<?php

namespace common\components\keyStorage;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class KeyStorage
 * @package common\components\keyStorage
 */
class KeyStorage extends Component
{
    /**
     * @var string
     */
    public $cachePrefix = '_keyStorage';
    /**
     * @var int
     */
    public $cachingDuration = 60;
    /**
     * @var string
     */
    public $modelClass = '\common\models\KeyStorageItem';

    /**
     * @var array Runtime values cache
     */
    private $values = [];

    /**
     * @param array $values
     */
    public function setAll(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $model = $this->getModel($key);
        if (!$model) {
            $model = new $this->modelClass;
            $model->key = $key;
        }
        $model->value = $value;
        if ($model->save(false)) {
            $this->values[$key] = $value;
            Yii::$app->cache->set($this->getCacheKey($key), $value, $this->cachingDuration);
            return true;
        };
        return false;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getModel($key)
    {
        $query = call_user_func($this->modelClass . '::find');
        return $query->where(['key' => $key])->one();
    }

    /**
     * @param $key
     * @return array
     */
    protected function getCacheKey($key)
    {
        return [
            __CLASS__,
            $this->cachePrefix,
            $key
        ];
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getAll(array $keys)
    {
        static $values = [];
        $result = [];
        foreach ($keys as $key) {
            if (isset($values[$key])) {
                $result[$key] = $values[$key];
                array_shift($keys);
            }
        }
        if (count($keys) == 0) {
            return $result;
        }

        $query = call_user_func($this->modelClass . '::find');
        foreach ($query->where(['key' => $keys])->all() as $item) {
            $values[$item->key] = $item->value;
            $result[$item->key] = $values[$item->key];
        }

        return $result;
    }

    /**
     * @param $key
     * @param null $default
     * @param bool $cache
     * @param int|bool $cachingDuration
     * @return mixed|null
     */
    public function get($key, $default = null, $cache = false, $cachingDuration = false)
    {
        if ($cache) {
            $cacheKey = $this->getCacheKey($key);
            $value = ArrayHelper::getValue($this->values, $key, false) ?: Yii::$app->cache->get($cacheKey);
            if ($value === false) {
                if ($model = $this->getModel($key)) {
                    $value = $model->value;
                    $this->values[$key] = $value;
                    Yii::$app->cache->set(
                        $cacheKey,
                        $value,
                        $cachingDuration === false ? $this->cachingDuration : $cachingDuration
                    );
                } else {
                    $value = $default;
                }
            }
        } else {
            $model = $this->getModel($key);
            $value = $model ? $model->value : $default;
        }
        return $value;
    }

    public function getPhoneValue($key, $default = null, $cache = true, $cachingDuration = false)
    {
        $value = $this->get($key, $default = null, $cache = true, $cachingDuration = false);
        $value = str_replace(' ', '', $value);
        $value = str_replace('(', '', $value);
        $value = str_replace(')', '', $value);
        $value = str_replace('-', '', $value);
        $value = str_replace('+7', '8', $value);
        return $value;
    }

    public function getPhoneValueEx($value)
    {
        $value = str_replace(' ', '', $value);
        $value = str_replace('(', '', $value);
        $value = str_replace(')', '', $value);
        $value = str_replace('-', '', $value);
        $value = str_replace('+7', '8', $value);
        return $value;
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys)
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $key
     * @param bool $cache
     * @return bool
     */
    public function has($key, $cache = true)
    {
        return $this->get($key, null, $cache) !== null;
    }

    /**
     * @param array $keys
     */
    public function removeAll(array $keys)
    {
        foreach ($keys as $key) {
            $this->remove($key);
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        unset($this->values[$key]);
        return call_user_func($this->modelClass . '::deleteAll', ['key' => $key]);
    }
}
