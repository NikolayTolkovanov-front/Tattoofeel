<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Product
 * @var $prices      common\models\ProductPrice
 * @var $categories common\models\ProductCategory[]
 * @var $categoriesConfig
 * @var $pricesError,
 */

$this->title = Yii::t('backend', 'Добавить продукт');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Каталог'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php echo $this->render('_form', [
    'model' => $model,
    'prices' => $prices,
    'categories' => $categories,
    'categoriesConfig' => $categoriesConfig,
    'pricesError' => $pricesError,
]) ?>
