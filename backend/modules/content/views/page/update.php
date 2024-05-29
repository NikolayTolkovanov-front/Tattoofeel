<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\page
 * @var $categories common\models\Page[]
 */

$this->title = t_b('Редактировать страницу: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Контент'), 'url' => ['/page/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Статические страницы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
