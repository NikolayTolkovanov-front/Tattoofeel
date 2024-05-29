<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Product
 * @var $prices      common\models\ProductPrice
 * @var $categories common\models\ProductCategory[]
 * @var $categoriesConfig
 * @var $pricesError
 */

$this->title = t_b('Редактировать продукт:') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php echo $this->render('_form', [
    'model' => $model,
    'prices' => $prices,
    'categories' => $categories,
    'categoriesConfig' => $categoriesConfig,
    'pricesError' => $pricesError,
]) ?>
