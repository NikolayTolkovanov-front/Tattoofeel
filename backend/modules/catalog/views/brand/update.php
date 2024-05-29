<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Brand
 * @var $categories common\models\Brand[]
 */

$this->title = t_b('Редактировать бренд: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Бренды'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
