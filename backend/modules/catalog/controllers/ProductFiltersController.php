<?php

namespace backend\modules\catalog\controllers;

use backend\modules\catalog\models\ImportFilters;
use common\models\ProductFilters;
use moonland\phpexcel\Excel;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\modules\catalog\models\search\ProductFiltersSearch;
use common\traits\FormAjaxValidationTrait;

class ProductFiltersController extends Controller
{
    use FormAjaxValidationTrait;


    /** @inheritdoc */
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
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $category = new ProductFilters();

        $this->performAjaxValidation($category);

        if ($category->load(Yii::$app->request->post()) && $category->save()) {
            return $this->redirect(['index']);
        }
        $searchModel = new ProductFiltersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $category,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return mixed
     * @throws
     */
    public function actionUpdate($id)
    {
        if ( is_null(Yii::$app->request->post('update')) && is_null(Yii::$app->request->post('save')) )
            Url::remember(Yii::$app->request->referrer,'index');

        $model = $this->findModel($id);

        $this->performAjaxValidation($model);

        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save() &&
            is_null(Yii::$app->request->post('update'))
        ) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionImport()
    {

        $import = new ImportFilters();

        if ($import->load(Yii::$app->request->post()) && $import->validate()) {
            if ($import->import())
                Yii::$app->session->setFlash('alert', [
                    'options' => ['class' => 'alert-success'],
                    'body' => "Импорт завершен успешно. Импортировано {$import->success_count} фильтров"
                ]);
            else
                Yii::$app->session->setFlash('alert', [
                    'options' => ['class' => 'alert-danger'],
                    'body' => "Импорт завершен неуспешно"
                ]);
        }

        return $this->render('import',[
            'import' => $import
        ]);
    }

    public function actionExport()
    {
        $export = new ImportFilters();

        Excel::export([
            'fileName' => 'Экспорт-фильтров-'.date('Y-m-d--h-i'),
            'models' => $export->exportData()
        ]);

        return $this->redirect(['import']);
    }

    /**
     * @param integer $id
     *
     * @return mixed
     * @throws
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     *
     * @return ProductFilters the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductFilters::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
