<?php

namespace common\models\query;

use common\models\UserClient;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserClientQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', UserClient::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => UserClient::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function notActive()
    {
        $this->andWhere(['status' => UserClient::STATUS_NOT_ACTIVE]);
        return $this;
    }
}
