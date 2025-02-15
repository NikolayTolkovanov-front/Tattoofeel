<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserClientTokenQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserClientTokenQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notExpired()
    {
        $this->andWhere(['>', 'expire_at', time()]);
        return $this;
    }

    /**
     * @return $this
     */
    public function expired()
    {
        $this->andWhere(['<=', 'expire_at', time()]);
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function byType($type)
    {
        $this->andWhere(['type' => $type]);
        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    public function byToken($token)
    {
        $this->andWhere(['token' => $token]);
        return $this;
    }
}
