<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\ProductSync
 */
$model->date = Yii::$app->formatter->asDateTime($model->date);

$this->title = t_b('Просмотр синхронизации: ') . ' ' . $model->date;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Синхронизация'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->date;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
