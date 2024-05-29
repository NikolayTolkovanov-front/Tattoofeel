<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\News
 * @var $categories common\models\News[]
 */

$this->title = t_b('Редактировать новость: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Контент'), 'url' => ['/news/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Новости'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
