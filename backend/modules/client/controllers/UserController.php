<?php

namespace backend\modules\client\controllers;

use backend\modules\client\models\search\UserClientSearch;
use backend\modules\client\models\UserClientForm;
use common\models\EmailTemplate;
use common\models\UserClient;
use common\models\UserClientProfile;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserClientController implements the CRUD actions for UserClient model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserClient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new UserClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserClientForm();
        $model->setScenario('create');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing UserClient model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = new UserClientForm();
        $model->setModel($this->findModel($id));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionProfile($id)
    {
        $user = $this->findModel($id);

        if (!isset($user->userProfile->id))
            throw new NotFoundHttpException('Профиль не найден');

        $model = UserClientProfile::findOne($user->userProfile->id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('profile', [
            'model' => $model
        ]);
    }

    public function actionProfileSync($id)
    {

        $model = UserClientProfile::findOne($id);

        if (!$model)
            throw new NotFoundHttpException('Профиль не найден');

        $model->sync();

        return $this->redirect(['profile', 'id' => $model->id]);
    }

    /**
     * Deletes an existing UserClient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $userModel = $this->findModel($id);

        $userModel->delete();
        $this->sendDeleteUserMail($userModel);

        return $this->redirect(['index']);
    }

    public function sendDeleteUserMail($user)
    {
        $user_email = $user->email;
 
        $body = EmailTemplate::render(EmailTemplate::DELETE_USER_TEMPLATE, [
        ]);

        return Yii::$app->mailer->compose()
            ->setTo($user_email)
            //->setTo('medvedgreez@yandex.ru')
            ->setFrom(env('ROBOT_EMAIL'))
            //->setReplyTo([$this->email => $this->name])
            ->setSubject('Tattoofeel: удаление аккаунта')
            //->setTextBody($body)
            ->setHtmlBody($body)
            ->send();
    }

    /**
     * Finds the UserClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserClient::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
