<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Stock
 * @var $categories common\models\Stock[]
 */

$this->title = t_b('Редактировать статью: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Контент'), 'url' => ['/article-n/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Статьи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
