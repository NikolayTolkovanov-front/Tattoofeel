<?php

namespace common\traits;

use Yii;
use yii\base\ExitException;
use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

trait FormAjaxValidationTrait
{

    /**
     * @param array|Model $model
     *
     * @throws ExitException
     */
    protected function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = ActiveForm::validate($model);
                Yii::$app->end();
            }
        }
    }

    /**
     * @param array|Model $mainModel
     * @param array|Model $innerModels
     * @param $className
     *
     * @throws ExitException
     */
    protected function performAjaxValidationMultiple($mainModel, $innerModels, $className)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {

            $resultMainModel = null;
            $resultInnerModels = null;

            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($mainModel && $mainModel->load(Yii::$app->request->post()))
                $resultMainModel = ActiveForm::validate($mainModel);

            if ($className::loadMultiple($innerModels, Yii::$app->request->post()))
                $resultInnerModels = ActiveForm::validateMultiple($innerModels);

            Yii::$app->response->data =
                (object) array_merge(
                    (array) $resultInnerModels,
                    (array) $resultMainModel
                );

            Yii::$app->end();
        }
    }

}
