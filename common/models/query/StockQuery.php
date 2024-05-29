<?php

namespace common\models\query;

use common\models\Stock;
use yii\db\ActiveQuery;

class StockQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere(['status' => Stock::STATUS_PUBLISHED]);
        $this->andWhere(['<', 'published_at', time()]);
        return $this;
    }
}
