<?php

namespace common\models\query;

use common\models\SliderMain;
use yii\db\ActiveQuery;

class SliderMainQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere(['status' => SliderMain::STATUS_PUBLISHED]);
        $this->andWhere(['<', 'published_at', time()]);
        return $this;
    }
}
