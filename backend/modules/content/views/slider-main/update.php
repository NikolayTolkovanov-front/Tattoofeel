<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Stock
 * @var $categories common\models\Stock[]
 */

$this->title = t_b('Редактировать баннер: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Контент'), 'url' => ['/slider-main/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Галерея на главной'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
