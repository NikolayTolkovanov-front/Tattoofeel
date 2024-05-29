<?php

namespace backend\modules\catalog;

use common\components\CategoryList;

/**
 * article module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\catalog\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->components = [
            'CategoryList' => [
                'class' => CategoryList::class,
            ]
        ];
    }
}
