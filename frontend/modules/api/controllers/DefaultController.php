<?php

namespace frontend\modules\api\controllers;

use yii\helpers\Url;
use yii\web\Response;

/**
 * @SWG\Swagger(
 *     basePath="/vue-api",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(version="1.0", title="Front-End API"),
 *     @SWG\Definition(
 *         definition="Response",
 *         title="Response",
 *         @SWG\Property(property="success", title="success", type="boolean"),
 *         @SWG\Property(property="message", title="message", type="string"),
 *     ),
 *     @SWG\Definition(
 *          definition="ResponseWithData",
 *          title="ResponseWithData",
 *          @SWG\Property(property="success", title="success", type="boolean"),
 *          @SWG\Property(property="message", title="message", type="string"),
 *          @SWG\Property(property="data", title="data", type="array", @SWG\Items()),
 *      )
 * )
 */
class DefaultController extends _Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        return $behaviors;
    }

    public function actions(): array
    {
        return [
            'docs' => [
                'class' => \yii2mod\swagger\SwaggerUIRenderer::class,
                'restUrl' => Url::to(['default/json-schema']),
            ],
            'json-schema' => [
                'class' => \yii2mod\swagger\OpenAPIRenderer::class,
                'scanDir' => [
                    \Yii::getAlias('@frontend/modules/api/controllers'),
                    //\Yii::getAlias('@frontend/modules/api/models'),
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        return 'Велкам ту аур пэидж.';
    }
}
