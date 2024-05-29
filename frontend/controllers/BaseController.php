<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    public function init() {

        if (!Yii::$app->user->isGuest) {

            $p = Yii::$app->user->identity->userProfile;
            if ($p->sale_change) {
                $p->sale_change = 0;
                $p->save();
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => 'Вам назначена новая скидка',
                    'options' => ['class' => 'alert-success']
                ]);
            }

        }

        parent::init();
    }
}
