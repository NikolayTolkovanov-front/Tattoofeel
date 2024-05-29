<?php

namespace common\models\query;

use common\models\News;
use yii\db\ActiveQuery;

class NewsQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere(['status' => News::STATUS_PUBLISHED]);
        $this->andWhere(['<', 'published_at', time()]);
        return $this;
    }
}
