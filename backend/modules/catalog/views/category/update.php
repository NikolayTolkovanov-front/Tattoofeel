<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\ProductCategory
 * @var $categories common\models\ProductCategory[]
 */

$this->title = t_b('Редактировать категорию продуктов: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Категории продуктов'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model,
    'categories' => $categories,
]) ?>
