<?php

namespace common\models\query;

use common\models\ProductCategory;
use yii\db\ActiveQuery;

class ProductCategoryQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere([ProductCategory::tableName().'.status' => ProductCategory::STATUS_PUBLISHED]);
        return $this;
    }

    public function order()
    {
        $this->addOrderBy([ProductCategory::tableName().'.order' => SORT_ASC]);

        return $this;
    }
}
