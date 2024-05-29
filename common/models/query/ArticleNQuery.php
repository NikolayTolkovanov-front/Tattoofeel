<?php

namespace common\models\query;

use common\models\ArticleN;
use yii\db\ActiveQuery;

class ArticleNQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere(['status' => ArticleN::STATUS_PUBLISHED]);
        $this->andWhere(['<', 'published_at', time()]);
        return $this;
    }
}
