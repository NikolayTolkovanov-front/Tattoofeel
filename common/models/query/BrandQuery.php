<?php

namespace common\models\query;

use common\models\Brand;
use yii\db\ActiveQuery;

class BrandQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere([Brand::tableName().'.status' => Brand::STATUS_PUBLISHED]);
        $this->andWhere([Brand::tableName().'.isMain' => 0]);
        return $this;
    }

    public function isMain()
    {
        $this->andWhere([Brand::tableName().'.status' => Brand::STATUS_PUBLISHED]);
        $this->andWhere([Brand::tableName().'.isMain' => 1]);
        return $this;
    }
}
