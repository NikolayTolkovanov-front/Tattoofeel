<?php

namespace api\modules\v1\controllers;

use api\modules\v1\resources\Product;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\rest\ActiveController;
use yii\rest\IndexAction;
use yii\rest\OptionsAction;
use yii\rest\CreateAction;
use yii\rest\UpdateAction;
use yii\rest\DeleteAction;
use yii\rest\Serializer;
use yii\rest\ViewAction;
use yii\web\HttpException;

class ProductController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'api\modules\v1\resources\Product';

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => [$this, 'prepareDataProvider']
            ],
            'view' => [
                'class' => ViewAction::class,
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel']
            ],
            'options' => [
                'class' => OptionsAction::class,

            ]
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $query = Product::find()->andWhere(['<>', Product::tableName() . '.status', 0])->andWhere(['<>', Product::tableName() . '.is_ms_deleted', 1]);
        $countQuery = clone $query;
        $count = $countQuery->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSizeLimit' => [0, 100]]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param $ms_id
     * @return array|null|\yii\db\ActiveRecord
     * @throws HttpException
     */
    public function findModel($id)
    {
        $model = Product::find()
            ->andWhere(['ms_id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404);
        }

        return $model;
    }
}
