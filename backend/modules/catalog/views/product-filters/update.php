<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\TypeEq
 * @var $categories common\models\TypeEq[]
 */

$this->title = t_b('Редактировать фильтр: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/product-filters']];
$this->params['breadcrumbs'][] = ['label' => t_b('Фильтры'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
