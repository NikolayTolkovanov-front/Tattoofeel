<?php declare(strict_types=1);

namespace api\controllers;

use common\models\UserClient;
use common\models\UserClientProfile;
use Exception;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

class ClientsController extends Controller
{
    public $enableCsrfValidation = false;

    private static function add8($number) {
        if (substr($number, 0, 2) === '+7') {
            return str_replace('+7', '8', $number);
        }
        return $number;
    }

    private static function remove8($number) {
        if (substr($number, 0, 1) === '8') {
            return preg_replace('/^8/', '+7', $number, 1);
        }
        return $number;
    }

    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        return [
            'docs' => [
                'class' => \yii2mod\swagger\SwaggerUIRenderer::class,
                'restUrl' => Url::to(['site/json-schema']),
            ],
            'json-schema' => [
                'class' => \yii2mod\swagger\OpenAPIRenderer::class,
                // Тhe list of directories that contains the swagger annotations.
                'scanDir' => [
                    Yii::getAlias('@api/modules/v1/controllers'),
                    Yii::getAlias('@api/modules/v1/models'),
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['site/docs']);
    }

    public function actionClientsFilter()
    {
        try {
            if (!Yii::$app->request->headers->get('authorization')) {
                throw new ForbiddenHttpException('Auth header missing');
            }
            if (Yii::$app->request->headers->get('authorization') !== env("CRM_AUTH_TOKEN")) {
                throw new ForbiddenHttpException('Auth header wrong');
            }
            $inputData = Yii::$app->request->bodyParams;
            if (!empty($inputData["email"]) || !empty($inputData["phone"])) {
                $clientProfile = UserClientProfile::find();
                if (!empty($inputData["email"])) {
                    $client = UserClient::find()->andWhere(['email' => $inputData["email"]])->one();
                    if (!$client) {
                        throw new NotFoundHttpException("User not found");
                    }
                    $clientProfile->andWhere([
                        'user_id' => $client->id
                    ]);
                }
                if (!empty($inputData["phone"])) {
                    $clientProfile->andWhere([
                        'or',
                        ['phone' => $this->add8($inputData["phone"])],
                        ['phone' => $this->remove8($inputData["phone"])],
                        ['phone_1' => $this->add8($inputData["phone"])],
                        ['phone_1' => $this->remove8($inputData["phone"])],
                    ]);
                }
                $clientProfile = $clientProfile->one();
                if (!$clientProfile) {
                    throw new NotFoundHttpException("User not found");
                }
                return $this->asJson([
                    'id' => $clientProfile->id,
                    'email' => $clientProfile->mail,
                    'phone' => $clientProfile->phone,
                    'ms_id' => $clientProfile->client_ms_id,
                    'additional_data' => [
                        'full_name' => $clientProfile->full_name,
                        'phone_1' => $clientProfile->phone_1,
                        'sale_ms_id' => $clientProfile->sale_ms_id,
                        'sale_brands' => $clientProfile->sale_brands ? json_decode($clientProfile->sale_brands) : null,
                    ],
                ]);
            } else {
                throw new BadRequestHttpException("Required: email or phone");
            }
        } catch (Exception $e) {
            if ($e instanceof HttpException) {
                Yii::$app->response->setStatusCode($e->statusCode);
            } else {
                Yii::$app->response->setStatusCode(500);
            }
            return $this->asJson([
                'error' => $e->getMessage(),
                'code' => Yii::$app->response->getStatusCode(),
                'status' => Yii::$app->response->getStatusCode(),
            ]);
        }
    }

    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof \HttpException) {
            Yii::$app->response->setStatusCode($exception->getCode());
        } else {
            Yii::$app->response->setStatusCode(500);
        }

        return $this->asJson(['error' => $exception->getMessage(), 'code' => $exception->getCode()]);
    }
}
