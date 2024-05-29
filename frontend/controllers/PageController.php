<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/4/14
 * Time: 2:01 PM
 */

namespace frontend\controllers;

use Yii;
use common\models\Page;
use frontend\models\ContactForm;
use frontend\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PageController extends BaseController
{
    public function actionView($slug)
    {

        $view = 'view';

        if ($slug === 'contact')
            $view = 'contact';

        $model = Page::find()->where(['slug' => $slug, 'status' => Page::STATUS_PUBLISHED])->one();
        if (!$model) {
            Yii::error("Page for slug = [".$slug."] not found");
            throw new NotFoundHttpException();
        }

        return $this->render($view, [
            'model' => $model,
            'form' =>  new ContactForm(),
            'productsRecently' => new ActiveDataProvider([
                'query' => Product::find()
                    ->preparePrice()
                    ->published()->limit(16),
                'pagination' => false,
            ]),
        ]);
    }


    /**
     * @return string|Response
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->contact(Yii::$app->params['adminEmail'])) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => 'Спасибо, что связались с нами. Мы ответим вам как можно скорее',
                    'options' => ['class' => 'alert-success']
                ]);
                return $this->refresh();
            }

            if ($model->validate())
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => 'Произошла ошибка при отправке электронной почты',
                    'options' => ['class' => 'alert-danger']
                ]);
        }

        $page = Page::find()->where(['slug' => 'contact', 'status' => Page::STATUS_PUBLISHED])->one();
        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $this->render('contact', [
            'form' => $model,
            'model' => $page,
            'productsRecently' => new ActiveDataProvider([
                'query' => Product::find()
                    ->preparePrice()
                    ->published()->limit(16),
                'pagination' => false,
            ]),
        ]);
    }
}
