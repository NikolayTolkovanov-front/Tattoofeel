<?php

namespace common\models\query;

use common\models\BlockWidget;
use yii\db\ActiveQuery;

class BlockWidgetQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere(['status' => BlockWidget::STATUS_PUBLISHED]);
        return $this;
    }
}
